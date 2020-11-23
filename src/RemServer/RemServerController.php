<?php
namespace Anax\RemServer;

use Anax\Commons\ContainerInjectableInterface;
use Anax\Commons\ContainerInjectableTrait;

/**
 * A controller for the REM Server.
 */
class RemServerController implements ContainerInjectableInterface
{
    use ContainerInjectableTrait;
    /**
     * Initiate the REM server before each action, if it has not already
     * some dataset(s).
     *
     * @return void
     */
    public function initialize() : void
    {
        $rem = $this->di->get("remserver");
        if (!$rem->hasDataset()) {
            $rem->init();
        }
    }
    /**
     * Init or re-init the REM Server.
     *
     * @return array
     */
    public function initActionGet() : array
    {
        $rem = $this->di->get("remserver");
        $rem->init();
        $json = [
            "message" => "The session is initiated with the default dataset(s).",
            "dataset" => $rem->getDefaultDataset(),
        ];
        return [$json];
    }
    /**
     * Get a dataset $key or parts of it by using the querystring.
     *
     * @param string $dataset identifier for the dataset
     *
     * @return array
     */
    public function getDataset($dataset) : array
    {
        $request = $this->di->get("request");
        $dataset = $this->di->get("remserver")->getDataset($dataset);
        $offset  = $request->getGet("offset", 0);
        $limit   = $request->getGet("limit", 25);
        $json = [
            "data" => array_slice($dataset, $offset, $limit),
            "offset" => $offset,
            "limit" => $limit,
            "total" => count($dataset)
        ];
        return [$json];
    }
    /**
     * Get one item from the dataset.
     *
     * @param string $dataset identifier for the dataset
     * @param int    $itemId  for the item to get
     *
     * @return array
     */
    public function getItem(string $dataset, int $itemId) : array
    {
        $item = $this->di->get("remserver")->getItem($dataset, $itemId);
        if (!$item) {
            return [["message" => "The item is not found."]];
        }
        return [$item];
    }
    /**
     * Show a message that the route is unsupported, a local 404.
     *
     * @return void
     */
    public function catchAll(...$args)
    {
        return [["message" => "404. The api does not support that."], 404];
    }
}
