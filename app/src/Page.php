<?php

namespace {

    use SilverStripe\AssetAdmin\Forms\UploadField;
    use SilverStripe\CMS\Model\SiteTree;
    use SilverStripe\Assets\Image;

    class Page extends SiteTree
    {
        private static $db = [];

        private static $has_one = [
            'HeroImage' => Image::class
        ];

        private static $owns = ['HeroImage'];

        public function getCMSFields()
        {
            $fields = parent::getCMSFields();
            $fields->addFieldToTab('Root.Main', UploadField::create('HeroImage'));
            return $fields;
        }
    }
}
