deep-thought CollectivesCallouts # cat CollectivesCallouts.php 
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
        private $gCalloutMark="CALLOUT_MARK"; 
    //private $gFileLog="plugins/CollectivesCallouts/.CollectivesCallouts.log";


   /**
     * Triggered after Pico has prepared the raw file contents for parsing
     *
     * @see DummyPlugin::onContentParsing()
     * @see Pico::parseFileContent()
     * @see DummyPlugin::onContentParsed()
     *
     * @param string &$markdown Markdown contents of the requested page
     */
    public function onContentPrepared(&$markdown)
    {
           if (isset($this->gFileLog)) file_put_contents($this->gFileLog, $markdown, FILE_APPEND | LOCK_EX);

                        $markdown=preg_replace('/[\n\r]:::/', $this->gCalloutMark, $markdown);

           if (isset($this->gFileLog)) file_put_contents($this->gFileLog, $markdown, FILE_APPEND | LOCK_EX);
    }


private function processNextCallout(&$aStart, &$aContent, $aCalloutFlavour) #TODO: Refactor using regex, make it caseinsensitve and what not
{
        $lReturnValue;

        /* find next */
        $lBeginTagPosStart=strpos($aContent, $this->gCalloutMark . " " .  $aCalloutFlavour, $aStart);

        /* Check if we found something */
        if ($lBeginTagPosStart!==false)
        {
                /* find the beginning of the enclosing HTML-tag to the left */
                #$lBeginHTMLTagPosStart=strpos($aContent, "<", -(strlen($aContent)-$lBeginTagPosStart+strlen(":: $aCalloutFlavour")));

                /* Extract Callout Begin-Tag, including HTML */
                #$lTagBegin=substr($aContent, $lBeginHTMLTagPosStart, $lBeginTagPosStart+strlen(":: $aCalloutFlavour")-$lBeginHTMLTagPosStart); // f.e. <p>:: info

                /* Find the begin of end-tag */
                $lEndTagPosStart=strpos($aContent, $this->gCalloutMark, $lBeginTagPosStart+strlen($this->gCalloutMark . " " . $aCalloutFlavour));

                /* Find the end of end-tag */
                #$lEndHTMLTagPosEnd=strpos($aContent, ">", $lEndHTMLTagPosStart+strlen(">::</"))+1; 
                $lEndTagPosEnd=$lEndTagPosStart+strlen($this->gCalloutMark); 

                /* Extract complete Callout */
                $lTagContent=substr($aContent, $lBeginTagPosStart, $lEndTagPosEnd-$lBeginTagPosStart); 
                        if (isset($this->gFileLog)) file_put_contents($this->gFileLog, "lTagContent: $lTagContent", FILE_APPEND | LOCK_EX);

                /* Remove Marks (beginning) */
                $lTagContentClean=substr($lTagContent, strlen($this->gCalloutMark . " " .  $aCalloutFlavour)); 
                        if (isset($this->gFileLog)) file_put_contents($this->gFileLog, "lTagContentClean: $lTagContentClean", FILE_APPEND | LOCK_EX);

                /* Remove Marks (end) */
                $lTagContentClean=substr($lTagContentClean, 0, -strlen($this->gCalloutMark)); 
                        if (isset($this->gFileLog)) file_put_contents($this->gFileLog, "lTagContentClean: $lTagContentClean", FILE_APPEND | LOCK_EX);

                /* Strip HTML-tags, remove whitespaces */
                $lTagContentClean=trim(strip_tags($lTagContentClean)); 

                /* Add HTML-Linebreaks */
                $lTagContentClean=nl2br($lTagContentClean); 

                /* Create new Callout Tag in HTML */
                $lNewTagString="<p class=\"callout_$aCalloutFlavour\">";
                $lNewTagString=$lNewTagString . $lTagContentClean . "</p>";
                        if (isset($this->gFileLog)) file_put_contents($this->gFileLog, "lNewTagString: $lNewTagString", FILE_APPEND | LOCK_EX);

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
        if (isset($this->gFileLog)) file_put_contents($this->gFileLog, $content, FILE_APPEND | LOCK_EX);

            $lCalloutFlavours=array("info", "success", "warn", "error");
        foreach ($lCalloutFlavours as $lCalloutFlavour)
        {
                $lLastStart=0;
                while ($this->processNextCallout($lLastStart, $content, $lCalloutFlavour))
                {
                        #echo("lLastStart: $lLastStart\n");
                                        #exit (1); // deb
                }// end while
        } // end foreach

        if (isset($this->gFileLog)) file_put_contents($this->gFileLog, $content, FILE_APPEND | LOCK_EX);

    } // end onContentParsed

} // end class
deep-thought CollectivesCallouts # 

