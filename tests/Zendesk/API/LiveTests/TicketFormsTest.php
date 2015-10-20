<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * Ticket Audits test class
 */
class TicketFormsTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    protected $id;

    public function setUP()
    {
        $form = $this->client->ticketForms()->create(array(
            'name' => 'Snowboard Problem',
            'end_user_visible' => true,
            'display_name' => 'Snowboard Damage',
            'position' => 2,
            'active' => true,
            'default' => false
        ));
        $this->assertEquals(is_object($form), true, 'Should return an object');
        $this->assertEquals(is_object($form->ticket_form), true, 'Should return an object called "ticket_form"');
        $this->assertGreaterThan(0, $form->ticket_form->id, 'Returns a non-numeric id for ticket_form');
        $this->assertEquals($form->ticket_form->name, 'Snowboard Problem', 'Name of test ticket form does not match');
        $this->assertEquals($form->ticket_form->display_name, 'Snowboard Damage',
            'Display name of test ticket form does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $form->ticket_form->id;
    }

    public function tearDown()
    {
        /*
         * First deactivate, then delete
         */
        $response = $this->client->ticketForm($this->id)->deactivate();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200',
            'Deactivate does not return HTTP code 200');
        $form = $this->client->ticketForm($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Delete does not return HTTP code 200');
    }

    public function testAll()
    {
        $forms = $this->client->ticketForms()->findAll();
        $this->assertEquals(is_object($forms), true, 'Should return an object');
        $this->assertEquals(is_array($forms->ticket_forms), true,
            'Should return an object containing an array called "ticket_forms"');
        $this->assertGreaterThan(0, $forms->ticket_forms[0]->id, 'Returns a non-numeric id in first ticket form');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $forms = $this->client->ticketForm($this->id)->find(); // ticket form #9881 must never be deleted
        $this->assertEquals(is_object($forms), true, 'Should return an object');
        $this->assertEquals(is_object($forms->ticket_form), true, 'Should return an object called "ticket_form"');
        $this->assertEquals($this->id, $forms->ticket_form->id, 'Returns an incorrect id in ticket form object');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate()
    {
        $form = $this->client->ticketForm($this->id)->update(array(
            'name' => 'Snowboard Fixed',
            'display_name' => 'Snowboard has been fixed'
        ));
        $this->assertEquals(is_object($form), true, 'Should return an object');
        $this->assertEquals(is_object($form->ticket_form), true, 'Should return an object called "ticket_form"');
        $this->assertGreaterThan(0, $form->ticket_form->id, 'Returns a non-numeric id for ticket_form');
        $this->assertEquals($form->ticket_form->name, 'Snowboard Fixed', 'Name of test ticket form does not match');
        $this->assertEquals($form->ticket_form->display_name, 'Snowboard has been fixed',
            'Display name of test ticket form does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        $id = $form->ticket_form->id;
        $stack = array($id);

        return $stack;
    }

    public function testReorder()
    {
        $allForms = $this->client->ticketForms()->findAll();
        $allIds = array();
        while (list($k, $form) = each($allForms->ticket_forms)) {
            $allIds[] = $form->id;
        }
        array_unshift($allIds, array_pop($allIds));
        $form = $this->client->ticketForms()->reorder($allIds);
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testClone()
    {
        $form = $this->client->ticketForm($this->id)->cloneForm();
        $this->assertEquals(is_object($form), true, 'Should return an object');
        $this->assertEquals(is_object($form->ticket_form), true, 'Should return an object called "ticket_form"');
        $this->assertGreaterThan(0, $form->ticket_form->id, 'Returns a non-numeric id for ticket_form');
        $this->assertEquals($form->ticket_form->name, 'Snowboard Problem', 'Name of test ticket form does not match');
        $this->assertEquals($form->ticket_form->display_name, 'Snowboard Damage',
            'Display name of test ticket form does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $id = $form->ticket_form->id;
        $response = $this->client->ticketForm($id)->deactivate();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200',
            'Deactivate does not return HTTP code 200');
        $form = $this->client->ticketForm($id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Delete does not return HTTP code 200');
    }

}
