<?php
/**
 * File Doc Comment
 *
 * PHP version 5.3
 *
 * @category   Application
 * @package    Configuration
 * @subpackage Bootstrap
 * @author     Alexander Kentner <akentner@lexsign.de>
 * @copyright  2011 Alexander Kentner
 * @license    http://no-licence-yet NLY Licence
 * @version    CVS: $Id: Bootstrap.php 301632 2010-07-28 01:57:56Z squiz $
 * @link       http://no-link-yet
 */

/**
 * Description of FeedController
 *
 * @category   Application
 * @package    Configuration
 * @subpackage Bootstrap
 * @author     Alexander Kentner <akentner@lexsign.de>
 * @copyright  2011 Alexander Kentner
 * @license    http://no-licence-yet NLY Licence
 * @version    Release: 0.1
 * @link       http://no-link-yet
 */
class Feed_IndexController extends Zend_Controller_Action
{

	public $dependencies = array(
		'cachemanager',
        'log'
    );


    public function preDispatch()
    {
        parent::preDispatch();
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
    }

    public function indexAction ()
    {
		$this->view->debug = __FILE__;

    }

	public function hr3comedyAction()
	{
        $url = 'http://www.hr-online.de/website/includes/medianew-playlist.xml.jsp?logic=start_multimedia_document_logic_37590724';

        $feed = new Zend_Feed_Writer_Feed;

		$feed->addItunesAuthor('Kall Napp');

        $feed->setTitle('hr3 Comedy');
        $feed->setLink('http://lexsignextension.home.akentner.de/feed/hr3comedy');
        $feed->setFeedLink('http://lexsignextension.home.akentner.de/feed/hr3comedy', 'rss');
        $feed->setDescription('hr3 Comedy');
        $feed->setImage(
			array(
				'uri' => 'http://www.hr-online.de/servlet/de.hr.cms.servlet.IMS?enc=d3M9aHJteXNxbCZibG9iSWQ9MTExNDMyOTkmd2lkdGg9MTIzJmlkPTM4MDIyMTU2',
				'title' => 'hr3 Comedy',
				'width' => 123,
				'heigth' => 101,
				'link' => 'http://lexsignextension.home.akentner.de/feed/hr3comedy',
			)
		);


        $feed->addAuthor(array(
            'name'  => 'hr3',
            'email' => 'a',
            'uri'   => 'http://www.hr3.de',
        ));
        $feed->setDateModified(time());

        foreach (new SimpleXMLElement(file_get_contents($url)) as $entry) {
            $info = array();
            $audio = reset($entry->audios);

            $info['key'] = (string) $audio->key;

            $date = new Zend_Date((string) $audio->date, 'dd.MM.yyyy HH:mm');

            foreach($audio->url as $url) {
                if ((string) $url['type'] === 'mp3') {
                    $uri = (string) $url;
                    $info = $this->_getInfoByUri($uri);
                }
            }


            $entry = $feed->createEntry();
            if (preg_match('/\d*\:\d{2}\:\d{2}/', $audio->duration)) {
                $entry->setItunesDuration((string) $audio->duration);
            }

            $entry->setTitle((string) $audio->title);
            $entry->setLink($uri);
            $entry->addAuthor(array(
                'name'  => '(string) $audio->source',
//                'email' => 'paddy@example.com',
//                'uri'   => 'http://www.example.com',
            ));
			$entry->addItunesAuthor((string) $audio->source);
			$entry->setItunesSummary((string) $audio->source);

            $entry->setDateModified($date->getTimestamp());
            $entry->setDateCreated($date->getTimestamp());
            $entry->setDescription((string) $audio->title);
            $entry->setContent((string) $audio->title);
            $entry->setEnclosure(
                array(
                    'uri' => $uri,
                    'type' => 'audio/mpeg',
                    'length' => $info['Content-Length'],
                )
            );


            $feed->addEntry($entry);
         }
        /**
        * Den ergebenden Feed in Atom 1.0 darstellen und $out zuordnen. Man kann
        * "atom" mit "rss" ersetzen um einen RSS 2.0 feed zu erstellen
        */
        echo $feed->export('rss');
	}


    private function _getInfoByUri($uri)
    {
        $info = array();
		$cache = $this->cachemanager->getCache('remotefile');
		$id = md5($uri);

		if(!$info = $cache->load($id)) {
			$ch = curl_init($uri);
			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

			$data = curl_exec($ch);
			curl_close($ch);

			if (preg_match('/Content-Length: (\d+)/', $data, $matches)) {
				$info['Content-Length'] = (int) $matches[1];
			}
			$this->log->debug(print_r($info, true));
			$cache->save($info, $id);
		}

        return $info;
    }

}
