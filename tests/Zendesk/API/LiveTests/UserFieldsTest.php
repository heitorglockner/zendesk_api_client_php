<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * UserFields test class
 */
class UserFieldsTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    protected $id, $number;

    public function setUp()
    {
        $this->number = strval(time());
        $userField = $this->client->userFields()->create(array(
            'type' => 'text',
            'title' => 'Support description' . $this->number,
            'description' => 'This field describes the support plan this user has',
            'position' => 0,
            'active' => true,
            'key' => 'support_description' . date("YmdHis")
        ));
        $this->assertEquals(is_object($userField), true, 'Should return an object');
        $this->assertEquals(is_object($userField->user_field), true, 'Should return an object called "user field"');
        $this->assertGreaterThan(0, $userField->user_field->id, 'Returns a non-numeric id for user field');
        $this->assertEquals($userField->user_field->title, 'Support description' . $this->number,
            'Title of test user field does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $userField->user_field->id;
    }

    public function testAll()
    {
        $userFields = $this->client->userFields()->findAll();
        $this->assertEquals(is_object($userFields), true, 'Should return an object');
        $this->assertEquals(is_array($userFields->user_fields), true,
            'Should return an object containing an array called "user_fields"');
        $this->assertGreaterThan(0, $userFields->user_fields[0]->id, 'Returns a non-numeric id for user_fields[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $userField = $this->client->userField($this->id)->find();
        $this->assertEquals(is_object($userField), true, 'Should return an object');
        $this->assertEquals(is_object($userField->user_field), true, 'Should return an object called "view"');
        $this->assertGreaterThan(0, $userField->user_field->id, 'Returns a non-numeric id for view');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate()
    {
        $userField = $this->client->userField($this->id)->update(array(
            'title' => 'Support description II' . $this->number
        ));
        $this->assertEquals(is_object($userField), true, 'Should return an object');
        $this->assertEquals(is_object($userField->user_field), true, 'Should return an object called "user_field"');
        $this->assertGreaterThan(0, $userField->user_field->id, 'Returns a non-numeric id for user_field');
        $this->assertEquals($userField->user_field->title, 'Support description II' . $this->number,
            'Name of test user field does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testReorder()
    {
        $view = $this->client->userFields()->reorder(array($this->id));
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function tearDown()
    {
        $this->assertGreaterThan(0, $this->id, 'Cannot find a user field id to test with. Did setUp fail?');
        $view = $this->client->userField($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}
