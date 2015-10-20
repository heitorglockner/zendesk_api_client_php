<?php

namespace Zendesk\API\LiveTests;

use Zendesk\API\Client;

/**
 * TopicComments test class
 */
class TopicCommentsTest extends BasicTest
{

    public function testCredentials()
    {
        parent::credentialsTest();
    }

    public function testAuthToken()
    {
        parent::authTokenTest();
    }

    protected $id, $topic_id, $forum_id;

    public function setUP()
    {
        /*
         * First start by creating a forum and a topic (we'll delete them later)
         */
        $forum = $this->client->forums()->create(array(
            'name' => 'My Forum',
            'forum_type' => 'articles',
            'access' => 'logged-in users'
        ));
        $this->forum_id = $forum->forum->id;

        $topic = $this->client->topics()->create(array(
            'forum_id' => $this->forum_id,
            'title' => 'My Topic',
            'body' => 'This is a test topic'
        ));
        $this->topic_id = $topic->topic->id;
        /*
         * Continue with the rest of the test...
         */
        $topicComment = $this->client->topic($this->topic_id)->comments()->create(array(
            'body' => 'A man walks into a bar'
        ));
        $this->assertEquals(is_object($topicComment), true, 'Should return an object');
        $this->assertEquals(is_object($topicComment->topic_comment), true,
            'Should return an object called "topic_comment"');
        $this->assertGreaterThan(0, $topicComment->topic_comment->id, 'Returns a non-numeric id for topic_comment');
        $this->assertEquals($topicComment->topic_comment->body, 'A man walks into a bar',
            'Body of test comment does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '201', 'Does not return HTTP code 201');
        $this->id = $topicComment->topic_comment->id;
    }

    public function testAll()
    {
        $topicComments = $this->client->topic($this->topic_id)->comments()->findAll();
        $this->assertEquals(is_object($topicComments), true, 'Should return an object');
        $this->assertEquals(is_array($topicComments->topic_comments), true,
            'Should return an object containing an array called "topic_comments"');
        $this->assertGreaterThan(0, $topicComments->topic_comments[0]->id,
            'Returns a non-numeric id for topic_comments[0]');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testFind()
    {
        $topicComment = $this->client->topic($this->topic_id)->comment($this->id)->find();
        $this->assertEquals(is_object($topicComment), true, 'Should return an object');
        $this->assertGreaterThan(0, $topicComment->topic_comment->id, 'Returns a non-numeric id for topic');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function testUpdate()
    {
        $topicComment = $this->client->topic($this->topic_id)->comment($this->id)->update(array(
            'body' => 'A man walks into a different bar'
        ));
        $this->assertEquals(is_object($topicComment), true, 'Should return an object');
        $this->assertEquals(is_object($topicComment->topic_comment), true,
            'Should return an object called "topic_comment"');
        $this->assertGreaterThan(0, $topicComment->topic_comment->id, 'Returns a non-numeric id for topic_comment');
        $this->assertEquals($topicComment->topic_comment->body, 'A man walks into a different bar',
            'Name of test topic does not match');
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

    public function teardown()
    {
        $this->assertGreaterThan(0, $this->id, 'Cannot find a topic comment id to test with. Did setUp fail?');
        $view = $this->client->topic($this->topic_id)->comment($this->id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
        /*
         * Clean-up
         */
        $topic = $this->client->forum($this->forum_id)->delete();
        $this->assertEquals($this->client->getDebug()->lastResponseCode, '200', 'Does not return HTTP code 200');
    }

}
