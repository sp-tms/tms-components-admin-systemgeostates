<?php

namespace Apps\Tms\Components\System\Geo\States;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class StatesComponent extends BaseComponent
{
    use DynamicTable;

    protected $geoStates;

    public function initialize()
    {
        $this->geoStates = $this->basepackages->geoStates->init();
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $countriesArr = $this->basepackages->geoCountries->geoCountries;

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $state = $this->basepackages->geoStates->getById($this->getData()['id']);

                $this->view->state = $state;
            }

            if (!$this->view->state) {
                return $this->throwIdNotFound();
            }

            $this->view->countries = [$countriesArr[$state['country_id']]];

            $this->view->pick('states/view');

            return;
        }

        $controlActions =
            [
                // 'includeQ'              => true,
                'actionsToEnable'       =>
                [
                    'view'      => 'system/geo/states',
                ]
            ];

        if ($this->request->isPost()) {
            $countries = [];

            foreach ($countriesArr as $countriesKey => $country) {
                $countries[$country['id']] = $country['name'] . ' (' . $country['id'] . ')';
            }

            $replaceColumns =
                [
                    'country_id'  =>
                        [
                            'html' => $countries
                        ]
                    ];

        } else {
            $replaceColumns = [];
        }

        $this->generateDTContent(
            $this->geoStates,
            'system/geo/states/view',
            null,
            ['name', 'state_code', 'country_id'],
            true,
            ['name', 'state_code', 'country_id'],
            $controlActions,
            null,
            $replaceColumns,
            'name',
            // $dtAdditionControlButtons
        );

        $this->view->pick('states/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        //
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        //
    }

    public function searchStateAction()
    {
        $this->requestIsPost();

        if ($this->postData()['search']) {
            $searchQuery = $this->postData()['search'];

            if (strlen($searchQuery) < 3) {
                return;
            }

            $states = $this->geoStates->searchStates($searchQuery);

            $states = msort($states, 'id');

            $this->addResponse(
                $this->geoStates->packagesData->responseMessage,
                $this->geoStates->packagesData->responseCode,
                ['states' => $states] ?? []
            );
        } else {
            $this->addResponse('Search Query Missing', 1);
        }
    }
}
