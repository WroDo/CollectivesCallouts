<?php
/**
 * This marks Nextcloud Collectives Callouts for later styling with CSS
 *
 * Source: https://github.com/WroDo/CollectivesCallouts
 *
 * SPDX-License-Identifier: MIT
 * License-Filename: LICENSE
 */

/**
 * @author  Heiko Kretschmer
 * @link    https://github.com/WroDo/CollectivesCallouts
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 0.1
 */
class CollectivesCallouts extends AbstractPicoPlugin
{
    /**
     * API version used by this plugin
     *
     * @var int
     */
    const API_VERSION = 3;
    //private $gFileLog="plugins/CollectivesCallouts/.CollectivesCallouts.log";


        private function processNextCallout(&$aStart, &$aContent) #TODO: Refactor using regex
        {
        $lReturnValue;

        /* find next */
        $lBeginTagPosStart=strpos($aContent, "<p>:: ", $aStart);

        /* Check if we found something */
        if ($lBeginTagPosStart!==false)
        {
                /* read ahead to end of begin-tag */
                $lBeginTagPosEnd=strpos($aContent, PHP_EOL, $lBeginTagPosStart+strlen("<p>:: "));

                /* Extract Callout Begin-Tag */
                $lTagBegin=substr($aContent, $lBeginTagPosStart, $lBeginTagPosEnd-$lBeginTagPosStart); // f.e. <p>:: info

                /* Extract Callout Begin-Tag flavour */
                $lTagBeginFlavour=substr($lTagBegin, strlen("<p>:: "), strlen($lTagBegin)-strlen("<p>:: ")); // f.e. <p>:: info

                /* Find the begin of end-tag */
                $lEndTagPosStart=strpos($aContent, "<p>::</p>", $lBeginTagPosEnd);

                /* Find the end of end-tag */
                $lEndTagPosEnd=$lEndTagPosStart+strlen("<p>::</p>");

                /* Extract Callout End-Tag */
                $lTagEnd=substr($aContent, $lEndTagPosStart, $lEndTagPosEnd-$lEndTagPosStart); 

                /* Extract Content between Callout Begin- and End-Tag */
                $lTagContent=substr($aContent, $lBeginTagPosEnd, $lEndTagPosStart-$lBeginTagPosEnd); 

                /* Clean-up Content between Callout Begin- and End-Tag */
                $lTagContentClean=nl2br(ltrim(rtrim(strip_tags($lTagContent)))); 

                /* Create new Callout Tag in HTML */
                $lNewTagString="<p class=\"callout_$lTagBeginFlavour\">";
                $lNewTagString=$lNewTagString . $lTagContentClean . "</p>";

                /* Replace Strings */
                $aContent=substr_replace($aContent, $lNewTagString, $lBeginTagPosStart, $lEndTagPosEnd-$lBeginTagPosStart);

                /* Advance for next search */
                $aStart=$lEndTagPosEnd;

                $lReturnValue=true;
        }
        else
        {
                $lReturnValue=false;
        }

        return($lReturnValue);
        } // end processNextCallout


    /**
     * Triggered after Pico has parsed the contents of the file to serve
     *
     * @see DummyPlugin::onContentParsing()
     * @see DummyPlugin::onContentPrepared()
     * @see Pico::getFileContent()
     *
     * @param string &$content parsed contents (HTML) of the requested page
     */
    public function onContentParsed(&$content)
    {
                $lPico = $this->getPico();

                if (23==23) // deb
                {
                        $lLastStart=0;
                        file_put_contents($this->gFileLog, $content, FILE_APPEND | LOCK_EX);

                        while ($this->processNextCallout($lLastStart, $content))
                        {
                                echo("lLastStart: $lLastStart\n");
                        }// end while
                }

                if (23==42) // deb
                if ($lPico)
                {
                        file_put_contents($this->gFileLog, $content, FILE_APPEND | LOCK_EX);
// This is how the Callout looks like in HTML:
//<dt>::: info</dt>
//<dt>I don't see the difference. Why does Pico CMS not show the images?</dt>
//<dd>
//<p>::</p>
                        $lLinesArray = explode(PHP_EOL, $content); /* we assume it keeps formatted nicely in lines */
                        foreach ($lLinesArray as &$aLine)
                        {
                                switch($aLine)
                                {
                                        case "<p>:: info":                              $aLine="<p class=\"callout_info\">";
                                                                                                        break;

                                        case "<p>:: success":                   $aLine="<p class=\"callout_success\">";
                                                                                                        break;

                                        case "<p>:: warn":                              $aLine="<p class=\"callout_warn\">";
                                                                                                        break;

                                        case "<p>:: error":                             $aLine="<p class=\"callout_error\">";
                                                                                                        break;

                                        case "<p>::</p>":                               $aLine="";
                                                                                                        break;

                                        case "<dd>::</dd>":                             $aLine="";
                                                                                                        break;
                                }
                        } // for each line
                    $content=implode("\r", $lLinesArray);
                } // if pico 
    } // end onContentParsed
}
