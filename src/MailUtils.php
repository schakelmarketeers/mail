<?php
declare(strict_types=1);

namespace Schakel\Mail;

use Schakel\Mail\Tracker\MailTrackerInterface;
use Html2Text\Html2Text;
use Pelago\Emogrifier;

/**
 * Defines a MailInterface, which can send emails to single users and will
 * allow for the tracking system to easily update itself when a mail is updated.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class MailUtils
{
    /**
     * This function loops through the HTML, retreiving all <style>-tags and
     * then inlines all CSS, since that's what must be done to send e-mails with
     * proper CSS.
     *
     * <em>quietly weeps</em>.
     *
     * @param string $html HTML e-mail to processs
     * @return string HTML with CSS inlined.
     */
    public static function createEmailHtml(string $html): string
    {
        // Load e-mail into DOMDocument
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $parseCompleted = $dom->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors(false);

        // If we cannot parse the HTML e-mail, return it as-is
        if (!$parseCompleted) {
            return $html;
        }

        $css = '';

        // Retrieve all style tags
        $styleTags = $dom->getElementsByTagName("style");
        foreach ($styleTags as $tag) {
            // If we somehow got a non-DOM element, continue
            if (!$tag instanceof \DOMNode) {
                continue;
            }

            // Get the content of the node
            $content = $tag->nodeValue;
            if (!empty($content)) {
                $css .= $content . PHP_EOL;
            }

            // And remove the node.
            $tag->parentNode->removeChild($tag);
        }

        // Adds all CSS to the head, in case a proper e-mail reader actually
        // understands how HTML works (looking at you, Thunderbird)
        if (!empty($css)) {
            $newStyle = $dom->createElement("style");
            $newStyle->setAttribute("type", "text/css");
            $newStyle->nodeValue = $css;

            $heads = $dom->getElementsByTagName("head");
            if ($heads->length > 0) {
                $heads->item(0)->appendChild($newStyle);
            }
        }

        // Returns HTML that's a complete godridden mess
        $html = $dom->saveHTML();

        // Now ask the Emogrifier to inline al CSS into HTML nodes.
        $emogrifier = new Emogrifier();
        $emogrifier->disableStyleBlocksParsing();
        $emogrifier->setHtml($html);
        $emogrifier->setCss($css);
        return $emogrifier->emogrify();
    }

    /**
     * Converts HTML e-mail to plain-text email. For those who dislike HTML
     * e-mails.
     *
     * @param string $text HTML mail
     * @return string Plain text mail
     */
    public static function createEmailPlain(string $text): string
    {
        // Replace non-breaking spaces with spaces, since they are somehow
        // converted to weird unicode.
        $text = str_replace('&nbsp;', ' ', $text);

        $parser = new Html2Text($text);
        return $parser->getText();
    }
}
