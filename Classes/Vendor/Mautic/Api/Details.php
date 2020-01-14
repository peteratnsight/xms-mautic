<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic, NP. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     MIT http://opensource.org/licenses/MIT
 */

namespace Mautic\Api;

/**
 * Details Context
 */
class Details extends Api
{

    /**
     * {@inheritdoc}
     */
    protected $endpoint = 'context';

    /**
     * Get a list of users available as lead owners
     *
     * @return array|mixed
     */
    public function getPageHits($mid,$page=FALSE)
    {
        if(!empty($page)) $page = base64_encode($page);
	//return $this->makeRequest('leads/'.$mid);

	return $this->makeRequest('context/'.$mid.'/pagehits/'.$page);
    }

    /**
     * Get isNewVisit Boolean
     *
     * @return array|mixed
     */
    public function isNewVisit($mid,$page=FALSE,$visithour=FALSE)
    {
        if(!empty($page)) $page = base64_encode($page);
	//return $this->makeRequest('leads/'.$mid);

	return $this->makeRequest('context/'.$mid.'/isnewhit/boolean/'.$page.(!empty($visithour)?"/".$visithour:""));
    }

    /**
     * Get Lead ID by Hash
     *
     * @param $hash
     */
    public function getLeadbyHash($hash)
    {
        return $this->makeRequest('context/get/'.$hash);
    }

}
