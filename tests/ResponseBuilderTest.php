<?php

namespace Muzzle;

use Muzzle\Messages\HtmlFixture;
use Muzzle\Messages\JsonFixture;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use VirtualFileSystem\FileSystem;

/**
 * @backupStaticAttributes enabled
 */
class ResponseBuilderTest extends TestCase
{

    /** @test */
    public function itCanBuildAResponse()
    {

        $response = (new ResponseBuilder)
            ->setStatus(HttpStatus::OK)
            ->setHeaders(['Content-Type' => 'application/json'])
            ->setBody(json_encode(['data' => ['message' => 'done']]))
            ->build();

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /** @test */
    public function itCanLoadAResponseBodyFromAFixtureFile()
    {

        $data = ['data' => ['message' => 'done']];
        $vfs = new FileSystem;
        $vfs->createDirectory('/fixtures');

        file_put_contents($vfs->path('fixtures/response.json'), json_encode($data));
        ResponseBuilder::setFixtureDirectory($vfs->path('fixtures'));

        $response = (new ResponseBuilder)
            ->setBodyFromFixture('response.json')
            ->build();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals($data, $response->decode());
    }

    /** @test */
    public function itCanCreateAJsonFixture()
    {

        $data = ['data' => ['message' => 'done']];
        $vfs = new FileSystem;
        $vfs->createDirectory('/fixtures');

        file_put_contents($vfs->path('fixtures/response.json'), json_encode($data));
        ResponseBuilder::setFixtureDirectory($vfs->path('fixtures'));

        $fixture = ResponseBuilder::fromFixture('response.json');

        $this->assertInstanceOf(JsonFixture::class, $fixture);
    }

    /** @test */
    public function itCanCreateAnHtmlFixture()
    {

        $data = '<span>some html</span>';
        $vfs = new FileSystem;
        $vfs->createDirectory('/fixtures');

        file_put_contents($vfs->path('fixtures/response.html'), $data);
        ResponseBuilder::setFixtureDirectory($vfs->path('fixtures'));

        $fixture = ResponseBuilder::fromFixture('response.html');

        $this->assertInstanceOf(HtmlFixture::class, $fixture);
    }

    /** @test */
    public function itCanSetTheBodyAsJsonFromAnArray()
    {

        $data = ['data' => ['message' => 'done']];
        $response = (new ResponseBuilder)->setJson($data)->build();

        $this->assertEquals(json_encode($data), $response->getBody()->getContents());
    }
}
