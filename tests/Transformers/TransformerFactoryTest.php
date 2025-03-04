<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Transformers;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
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
    use MockeryPHPUnitIntegration;

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

    public function testCreateFromModel(): void
    {
        /** @var TransformerStub $transformer */
        $transformer = $this->transformerFactory->createFromModel($this->modelMock);
        $this->assertInstanceOf(TransformerStub::class, $transformer);
        $this->assertEquals($this->urlGenerator, $transformer->getUrlGenerator());
        $this->assertEquals($this->dependency, $transformer->getDependency());
    }

    public function testCreateFromModelIgnoresOtherMappedDependencies(): void
    {
        $this->transformerFactory->setMapping([get_class($this->modelMock) => OtherTransformerStub::class]);

        /** @var TransformerStub $transformer */
        $transformer = $this->transformerFactory->createFromModel($this->modelMock);
        $this->assertInstanceOf(OtherTransformerStub::class, $transformer);
        $this->assertEquals($this->urlGenerator, $transformer->getUrlGenerator(), 'Abstract dependency should be set');
        $this->assertNull($transformer->getDependency(), 'TransformerStub dependency should not be set');
    }

    public function testCreateFromModelWithInvalidModel(): void
    {
        $this->expectException(TransformerException::class);
        $this->transformerFactory->createFromModel(new stdClass());
    }

    public function testCreateTransformerItem(): void
    {
        $this->assertInstanceOf(
            TransformerItem::class,
            $this->transformerFactory->createTransformerItem($this->modelMock),
        );
    }

    public function testCreateTransformerCollection(): void
    {
        $collection = Mockery::mock(Collection::class, ['offsetExists' => false, 'offsetGet' => null]);
        $this->assertInstanceOf(
            TransformerCollection::class,
            $this->transformerFactory->createTransformerCollection($collection),
        );
    }
}
