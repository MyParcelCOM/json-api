<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Mockery;
use MyParcelCom\JsonApi\Tests\Stubs\OtherTransformerStub;
use MyParcelCom\JsonApi\Tests\Stubs\TransformerStub;
use MyParcelCom\JsonApi\Transformers\AbstractTransformer;
use MyParcelCom\JsonApi\Transformers\TransformerCollection;
use MyParcelCom\JsonApi\Transformers\TransformerException;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use MyParcelCom\JsonApi\Transformers\TransformerItem;
use PHPUnit\Framework\TestCase;
use stdClass;

class TransformerFactoryTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected TransformerFactory $transformerFactory;

    protected Model $modelMock;

    protected UrlGenerator $urlGenerator;

    protected string $dependency = 'Some random dependency';

    protected function setUp(): void
    {
        parent::setUp();
        $this->urlGenerator = Mockery::mock(UrlGenerator::class);
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
    public function testCreateFromModelIgnoresOtherMappedDependencies()
    {
        $this->transformerFactory->setMapping([get_class($this->modelMock) => OtherTransformerStub::class]);

        /** @var TransformerStub $transformer */
        $transformer = $this->transformerFactory->createFromModel($this->modelMock);
        $this->assertInstanceOf(OtherTransformerStub::class, $transformer);
        $this->assertEquals($this->urlGenerator, $transformer->getUrlGenerator(), 'Abstract dependency should be set');
        $this->assertNull($transformer->getDependency(), 'TransformerStub dependency should not be set');
    }

    /** @test */
    public function testCreateFromModelWithInvalidModel()
    {
        $this->expectException(TransformerException::class);
        $this->transformerFactory->createFromModel(new stdClass());
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
