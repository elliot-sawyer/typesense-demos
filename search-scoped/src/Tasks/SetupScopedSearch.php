<?php
namespace ElliotSawyer\TypesenseSearch\ScopedSearch;

use Page;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Group;
use SilverStripe\Security\Member;

class SetupScopedSearch extends BuildTask
{
    private static $segment = 'SetupScopedSearch';
    private static $speakers = [];
    private static $default_password = 'Test00!!';
    public function getTitle()
    {
        return 'Setup scoped search';
    }

    public function getDescription()
    {
        return 'Setup scoped search';
    }
    public function run($request = null)
    {
        $this->makeMembers();
        $this->clearMemberKeys();
        $parent = $this->createParentPage();
        $this->createSharedDocuments($parent);
    }

    public function makeMembers()
    {
        $group = Group::get()->find('Title', 'Demo users')
            ?: Group::create(['Title' => 'Demo users']);
        $group->write();
        $speakers = $this->config()->speakers;
        foreach($speakers as $speaker) {
            $member = Member::get()->filter($speaker)->first()
                ?: Member::create($speaker);
            if(!($member && $member->ID)) {
                $member->Password = $this->config()->default_password;
                $group->Members()->add($member);
                DB::alteration_message('...created ' . $member->Email);
            }
        }
        DB::alteration_message(count($speakers) . ' speakers loaded');
    }

    /**
     * Create the Shared Documents parent page
     *
     * @return Page
     */
    protected function createParentPage(): Page
    {
        $parent = Page::get()->find('Title', 'Demos');
        $parentID = $parent->ID;
        $page = ScopedSearchPage::get()->filter(['Title' => 'Scoped Search', 'ParentID' => $parentID])->first();
        if (!($page && $page->ID)) {
            $page = ScopedSearchPage::create();
            $page->Title = 'Scoped Search';
            $page->Content = '<p>This section contains documents shared with specific users.</p>';
            $page->publishSingle();
        }

        return $page;
    }

    /**
     * Create 100 SharedDocument pages
     *
     * @param Page $parent
     * @param \SilverStripe\ORM\DataList $members
     * @return \SilverStripe\ORM\DataList
     */
    protected function createSharedDocuments(ScopedSearchPage $parent)
    {
        $loremIpsum = [
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.',
            'Duis aute irure dolor in reprehenderit in voluptate velit esse.',
            'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui.',
            'Nisi ut aliquip ex ea commodo consequat deserunt mollit anim id.',
            'Curabitur pretium tincidunt lacus nulla gravida orci a odio.',
            'Cum sociis natoque penatibus et magnis dis parturient montes.',
            'Nascetur ridiculus mus mauris vitae ultricies leo integer.',
            'Malesuada fames ac turpis egestas sed tempus urna et pharetra.',
        ];

        for ($i = 1; $i <= 67; $i++) {
            shuffle($loremIpsum);
            $title = "Document " . str_pad($i, 3, '0', STR_PAD_LEFT);
            $opts = ['Title' => $title, 'ParentID' => $parent->ID, 'CanViewType' => 'OnlyTheseMembers'];
            $page = SharedDocument::get()->filter($opts)->first();
            if($page && $page->ID && $page->ViewerMembers()->Count() == 0) {
                dd($page->ID, $page->$page->ViewerMembers()->Count());
                continue;
            }
            $rnd = random_int(2, 5);
            $members = Group::get()->find('Title', 'Demo users')
                ->Members()
                ->shuffle()
                ->limit($rnd);
            $content = array_slice($loremIpsum, 0, $rnd);

            $page = SharedDocument::create();
            foreach($members as $owner) {
                $page->ViewerMembers()->add($owner);
            }
            $page->Title = $title;
            $page->Content = implode(' ', $content);
            $page->CanViewType = 'OnlyTheseMembers';
            $page->ParentID = $parent->ID;

            $page->publishSingle();
            DB::alteration_message('...published '.$page->Title . ' to parent ' . $parent->Title);
        }
        DB::alteration_message($i . ' shared documents loaded');
    }

    public function clearMemberKeys()
    {
        $group = Group::get()->find('Title', 'Demo users')
            ?: Group::create(['Title' => 'Demo users']);
        $members = $group->Members();
        foreach($members as $member) {
            $member->TypesenseAPISearchKey = null;
            $member->TypesenseAPISearchKeyExpires = null;
            $member->write();
        }

        DB::alteration_message('Cleared existing scoped search keys');
    }
}
