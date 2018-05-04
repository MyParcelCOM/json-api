<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Mockery;
use MyParcelCom\JsonApi\Interfaces\UrlGeneratorInterface;
use MyParcelCom\JsonApi\Tests\Stubs\TransformerStub;
use MyParcelCom\JsonApi\Transformers\AbstractTransformer;
use MyParcelCom\JsonApi\Transformers\TransformerCollection;
use MyParcelCom\JsonApi\Transformers\TransformerException;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use MyParcelCom\JsonApi\Transformers\TransformerItem;
use PHPUnit\Framework\TestCase;

class TransformerFactoryTest extends TestCase
{
    /** @var TransformerFactory */
    protected $transformerFactory;

    /** @var Model */
    protected $modelMock;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    protected $dependency = 'Some random dependency';

    protected function setUp()
    {
        parent::setUp();
        $this->urlGenerator = Mockery::mock(UrlGeneratorInterface::class);
        $this->modelMock = Mockery::mock(Model::class);
        $this->transformerFactory = (new TransformerFactory())
            ->setDependencies([
                AbstractTransformer::class => [
                    'setUrlGenerator' => function () {
                        return $this->urlGenerator;
                    },
                ],
                TransformerStub::class     => [
                    'setDependency' => function () {
                        return $this->dependency;
                    },
                ],
            ]);
        $this->transformerFactory->setMapping([get_class($this->modelMock) => TransformerStub::class]);
    }

    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testCreateFromModel()
    {
        /** @var TransformerStub $transformer */
        $transformer = $this->transformerFactory->createFromModel($this->modelMock);
        $this->assertInstanceOf(TransformerStub::class, $transformer);
        $this->assertEquals($this->urlGenerator, $transformer->getUrlGenerator());
        $this->assertEquals($this->dependency, $transformer->getDependency());
    }

    /** @test */
    public function testCreateFromModelWithInvalidModel()
    {
        $this->expectException(TransformerException::class);
        $this->transformerFactory->createFromModel(new \stdClass());
    }

    /** @test */
    public function testCreateTransformerItem()
    {
        $this->assertInstanceOf(
            TransformerItem::class,
            $this->transformerFactory->createTransformerItem($this->modelMock)
        );
    }

    /** @test */
    public function testCreateTransformerCollection()
    {
        $collection = Mockery::mock(Collection::class, ['offsetExists' => false, 'offsetGet' => null]);
        $this->assertInstanceOf(
            TransformerCollection::class,
            $this->transformerFactory->createTransformerCollection($collection)
        );
    }
}
