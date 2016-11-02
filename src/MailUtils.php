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

        // Create a variable for all CSS in the page and a variable for all
        // style nodes that have been appended to the $css variable, which will
        // then be removed.
        $css = '';
        $removeList = [];

        // Retrieve all style tags and loop through them
        $styleTags = $dom->getElementsByTagName("style");
        foreach ($styleTags as $node) {
            // If we somehow got a non-DOM element, continue
            if (!$node instanceof \DOMNode) {
                continue;
            }

            // If the node isn't empty, add the contents
            if (!empty($node->nodeValue)) {
                $css .= $node->nodeValue . PHP_EOL;
            }

            // And add the node to the removal list.
            $removeList[] = $node;
        }

        // Remove all nodes in the $removeList
        foreach ($removeList as $node) {
            $node->parentNode->removeChild($node);
        }

        // Find a head tag (or create one))
        $headNodes = $dom->getElementsByTagName("head");
        if ($headNodes->length > 0) {
            $headNode = $headNodes->item(0);
        } else {
            $headNode = $dom->createElement('head');
            $dom->insertBefore($headNode, $dom->firstChild);
        }

        // And add a short link back to this project.
        $generator = $dom->createElement('meta');
        $generator->setAttribute('name', 'generator');
        $generator->setAttribute(
            'value',
            'Schakel Marketeers Mail, https://github.com/SchakelMarketeers/mail'
        );
        $headNode->appendChild($generator);

        // Adds all CSS to the head, in case a proper e-mail reader actually
        // understands how HTML works.
        if (!empty($css)) {
            $styleNode = $dom->createElement("style");
            $styleNode->setAttribute("type", "text/css");
            $styleNode->setAttribute("media", "all");

            $styleNode->nodeValue = $css;

            $headNode->appendChild($styleNode);
        }

        // Convert the DOMDocument back to HTML
        $html = $dom->saveHTML();

        // Now ask the Emogrifier to inline al CSS into the HTML nodes.
        $emogrifier = new Emogrifier;
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
