<?php
namespace Anax\RemServer;

use Anax\Session\SessionInterface;

/**
 * REM Server using session to store information.
 */
class RemServer
{
    /**
     * @var array  $dataset the dataset to add as default dataset.
     * @var object $session inject a reference to the session.
     * @var string $key to use when storing in session.
     */
    private $dataset = [];
    protected $session;
    const KEY = "remserver";
    /**
     * Inject dependency to $session.
     *
     * @param SessionInterface $session object representing session.
     *
     * @return self
     */
    public function injectSession(SessionInterface $session) : object
    {
        $this->session = $session;
        return $this;
    }
    /**
     * Set the default dataset to use.
     *
     * @param array $dataset array with absolute paths to json files to load.
     *
     * @return self
     */
    public function setDefaultDataset(array $dataset) : object
    {
        $this->dataset = $dataset;
        return $this;
    }
    /**
     * Get the default dataset that is used.
     *
     * @return self
     */
    public function getDefaultDataset() : array
    {
        return $this->dataset;
    }
    /**
     * Fill the session with default data that are read from files.
     *
     * @throws Anax\RemServer\Exception when bad configuration.
     *
     * @return self
     */
    public function init()
    {
        $json = [];
        foreach ($this->dataset as $file) {
            if (!(is_file($file) && is_readable($file))) {
                throw new Exception("File '$file' for dataset not readable.");
            }
            $content = file_get_contents($file);
            $key = pathinfo($file, PATHINFO_FILENAME);
            $json[$key] = json_decode($content, true);
        }
        $this->session->set(self::KEY, $json);
        return $this;
    }
    /**
     * Check if there is a dataset stored.
     *
     * @return boolean true if dataset exists in session, else false
     */
    public function hasDataset()
    {
        return($this->session->has(self::KEY));
    }
    /**
     * Get (or create) a subset of data.
     *
     * @param string $key for data subset.
     *
     * @return array with the dataset
     */
    public function getDataset($key)
    {
        $data = $this->session->get(self::KEY);
        $set = isset($data[$key])
            ? $data[$key]
            : [];
        return $set;
    }
    /**
     * Save (the modified) dataset.
     *
     * @param string $key     for data subset.
     * @param array  $dataset the data to save.
     *
     * @return self
     */
    public function saveDataset($key, $dataset)
    {
        $data = $this->session->get(self::KEY);
        $data[$key] = $dataset;
        $this->session->set(self::KEY, $data);
        return $this;
    }
    /**
     * Get an item from a dataset.
     *
     * @param string $key    for the dataset
     * @param string $itemId for the item to get
     *
     * @return array|null array with item if found, else null
     */
    public function getItem($key, $itemId)
    {
        $dataset = $this->getDataset($key);
        foreach ($dataset as $item) {
            if ($item["id"] === $itemId) {
                return $item;
            }
        }
        return null;
    }
}
