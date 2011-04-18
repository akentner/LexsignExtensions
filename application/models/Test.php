<?php
/** 
 * @author akentner
 * 
 * 
 */

/**
 * @Entity
 * @Table(name="test")
 */
class Application_Model_Test
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string") 
     */
    public $name;
}
