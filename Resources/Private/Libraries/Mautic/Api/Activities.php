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
 * Activities Context
 */
class Activities extends Api
{

    /**
     * {@inheritdoc}
     */
    protected $endpoint = 'activities';

    /**
     *
     * @return array|mixed
     */
    public function getActivities($id)
    {
	return $this->makeRequest('activities/'.$id);
    }

    /**
     *
     * @return array|mixed
     */
    public function getActivitiesbyLead($leadid)
    {
        return $this->makeRequest('activities/getfor/'.$leadid);
    }

    public function createActivity($id, array $parameters) 
    {
        return $this->makeRequest($this->endpoint.'/createfor/'.$id, $parameters, 'POST');
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->actionNotSupported('delete');
    }

}
