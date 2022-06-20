<?php

/**
 * This Software is the property of Data Development and is protected
 * by copyright law - it is NOT Freeware.
 * Any unauthorized use of this software without a valid license
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 * http://www.shopmodule.com
 *
 * @copyright (C) D3 Data Development (Inh. Thomas Dartsch)
 * @author        D3 Data Development - Daniel Seifert <support@shopmodule.com>
 * @link          http://www.oxidmodule.com
 */

namespace D3\LinkmobilityClient;

class Url implements UrlInterface
{
    public $baseUri = 'https://api.linkmobility.eu/rest';

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    /**
     * @param float  $lat
     * @param float  $lng
     * @param float  $radius
     * @param string $sort
     * @param string $type
     *
     * @return string
     */
    public function getListUrl(float $lat, float $lng, float $radius, string $sort, string $type): string
    {
        $query = http_build_query(
            [
                'lat'   => $lat,
                'lng'   => $lng,
                'rad'   => $radius,
                'sort'  => $sort,
                'type'  => $type,
                'apikey'=> $this->apiKey
            ]
        );
        return "list.php?$query";
    }

    /**
     * @param $stationId
     *
     * @return string
     */
    public function getStationDetailUrl($stationId): string
    {
        $query = http_build_query(
            [
                'id'    => $stationId,
                'apikey'=> $this->apiKey
            ]
        );
        return "detail.php?$query";
    }

    /**
     * @param array $stationList
     *
     * @return string
     * @throws ApiException
     */
    public function getPricesUrl(array $stationList): string
    {
        if (count($stationList) < 1 || count($stationList) > 10) {
            throw new ApiException('Preisabfrage darf nur zwischen 1 und 10 Stationen beinhalten');
        }

        $query = http_build_query(
            [
                'ids'   => implode( ',', $stationList),
                'apikey'=> $this->apiKey
            ]
        );

        return "prices.php?$query";
    }

    /**
     * @return string
     */
    public function getComplaintUrl(): string
    {
        $query = http_build_query(['apikey'=> $this->apiKey]);

        return $this->baseUri . "complaint.php?$query";
    }
}