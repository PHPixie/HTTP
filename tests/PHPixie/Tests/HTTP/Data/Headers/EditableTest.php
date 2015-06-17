<?php

namespace PHPixie\Tests\HTTP\Data\Headers;

/**
 * @coversDefaultClass PHPixie\HTTP\Data\Headers\Editable
 */
class EditableTest extends \PHPixie\Tests\HTTP\Data\HeadersTest
{
    /**
     * @covers ::set
     * @covers ::<protected>
     */
    public function testSet()
    {
        $headers = $this->headerArray;
        unset($headers['Fairy']);
        
        $headers['fairy'] = array('Trixie');
        $this->headers->set('fairy', 'Trixie');
        $this->assertHeaders($headers);
        
        $headers['fairy'] = array('Trixie', 'Pixie');
        $this->headers->set('fairy', array('Trixie', 'Pixie'));
        $this->assertHeaders($headers);
        
        $headers['spell'] = array('Rain');
        $this->headers->set('spell', 'Rain');
        $this->assertHeaders($headers);
    }
    
    /**
     * @covers ::add
     * @covers ::<protected>
     */
    public function testAdd()
    {
        $headers = $this->headerArray;
        
        $headers['Fairy'][] = 'Trixie';
        $this->headers->add('fairy', 'Trixie');
        $this->assertHeaders($headers);
        
        $headers['Fairy'][] = 'Pixie';
        $headers['Fairy'][] = 'Blum';
        $this->headers->add('fairy', array('Pixie', 'Blum'));
        $this->assertHeaders($headers);
        
        $headers['spell'] = array('Rain');
        $this->headers->add('spell', 'Rain');
        $this->assertHeaders($headers);
    }
    
    /**
     * @covers ::remove
     * @covers ::<protected>
     */
    public function testRemove()
    {
        $headers = $this->headerArray;
        unset($headers['Fairy']);
        
        $this->headers->remove('fairy');
        $this->assertHeaders($headers);
        
        $this->headers->remove('Blum');
        $this->assertHeaders($headers);
    }
    
    protected function headers()
    {
        return new \PHPixie\HTTP\Data\Headers\Editable($this->headerArray);
    }
}