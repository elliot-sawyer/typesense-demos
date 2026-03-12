<?php

namespace {

    use SilverStripe\CMS\Controllers\ContentController;
    use SilverStripe\View\Requirements;

    /**
     * @template T of Page
     * @extends ContentController<T>
     */
    class PageController extends ContentController
    {
        /**
         * An array of actions that can be accessed via a request. Each array element should be an action name, and the
         * permissions or conditions required to allow the user to access it.
         *
         * <code>
         * [
         *     'action', // anyone can access this action
         *     'action' => true, // same as above
         *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
         *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
         * ];
         * </code>
         *
         * @var array
         */
        private static $allowed_actions = [];

        protected function init()
        {
            parent::init();
            // You can include any CSS or JS required by your project here.
            // See: https://docs.silverstripe.org/en/developer_guides/templates/requirements/
        }

        public function HeroImageCSS()
        {
            if($this->HeroImageID) {
                $heroImage = $this->HeroImage();
                $heroimageCSS = sprintf(".jumbotron.hero-image[data-heroimage-id='%d'] {
                        background-image: radial-gradient(circle at top left, rgba(0,0,0,0.99),rgba(0,0,0,0.0)), url('%s');
                        background-position: 50.0%% 50.0%%;
                        min-height: 360px;
                    }",
                    $this->HeroImageID,
                    $heroImage?->getURL() ?? '',
                );
            $css1 = sprintf(".%s .jumbotron.hero-image:before {
                        content: '';
                        position: absolute;
                        inset: 0;
                        z-index: -1;
                        backdrop-filter: blur(20px);
                        animation: blurry 3s infinite alternate;
                    }
                    .%s .jumbotron.hero-image{
                        position:relative;
                        overflow: hidden;
                        backdrop-filter: blur(20px);
                        animation: blurry 5s infinite alternate;
                    }
                    @keyframes blurry {
                        from {
                            backdrop-filter: blur(1px);
                        }

                        to {
                            backdrop-filter: blur(30px);
                        }
                    }
                    ",
                    $this->dbObject('ClassName')->ShortName,
                    $this->dbObject('ClassName')->ShortName,
                );
                Requirements::customCSS($css1);
                Requirements::customCSS($heroimageCSS);
            }
        }
    }
}
