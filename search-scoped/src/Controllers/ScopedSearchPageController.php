<?php
namespace ElliotSawyer\TypesenseSearch\ScopedSearch;

use ElliotSawyer\SilverstripeTypesense\Typesense;
use ElliotSawyer\TypesenseSearch\TypesenseSearchPageController;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Security\Security;

class ScopedSearchPageController extends TypesenseSearchPageController
{
    public function init()
    {
        parent::init();
        if(!($member = Security::getCurrentUser())) {
            return;
        }
        $parentKey = $this->data()->SearchAPIKey;
        if($member && !$member->TypesenseAPISearchKey) {
            $client = Typesense::client();
            $expiry = strtotime("+24 hours");

            if(!$member->TypesenseAPISearchKey || $member->TypesenseAPISearchKeyExpires > DBDatetime::now()) {
                $scopedKey = $client->keys->generateScopedSearchKey($parentKey, [
                    'filter_by' => 'Owner.ID:'.$member->ID,
                    'expires_at' => $expiry
                ]);
                if($scopedKey) {
                    $member->TypesenseAPISearchKey = $scopedKey;
                    $member->TypesenseAPISearchKeyExpires = $expiry;
                    $member->write();
                }
            }

        }
    }

}
