<?php declare(strict_types=1);

namespace MyParcelCom\Transformers\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Mockery;
use MyParcelCom\Common\Contracts\UrlGeneratorInterface;
use MyParcelCom\Transformers\Tests\Stubs\TransformerStub;
use MyParcelCom\Transformers\TransformerCollection;
use MyParcelCom\Transformers\TransformerException;
use MyParcelCom\Transformers\TransformerFactory;
use MyParcelCom\Transformers\TransformerItem;
use PHPUnit\Framework\TestCase;

class TransformerFactoryTest extends TestCase
{
    /** @var TransformerFactory */
    protected $transformerFactory;

    /** @var Model */
    protected $modelMock;

    public function setUp()
    {
        parent::setUp();
        $this->modelMock = Mockery::mock(Model::class);
        $this->transformerFactory = new TransformerFactory(Mockery::mock(UrlGeneratorInterface::class));
        $this->transformerFactory->setMapping([get_class($this->modelMock) => TransformerStub::class]);
    }

    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    /** @test */
    public function testCreateFromModel()
    {
        $this->assertInstanceOf(TransformerStub::class, $this->transformerFactory->createFromModel($this->modelMock));
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
        $this->assertInstanceOf(TransformerItem::class, $this->transformerFactory->createTransformerItem($this->modelMock));
    }

    /** @test */
    public function testCreateTransformerCollection()
    {
        $collection = Mockery::mock(Collection::class, ['offsetExists' => false, 'offsetGet' => null]);
        $this->assertInstanceOf(TransformerCollection::class, $this->transformerFactory->createTransformerCollection($collection));
    }
}
