<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link https://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.1 (https://modulestudio.de).
 */

namespace Zikula\MultisitesModule\Imagine\Cache\Base;

use Liip\ImagineBundle\Imagine\Cache\SignerInterface;

/**
 * Temporary dummy signer until https://github.com/liip/LiipImagineBundle/issues/837 has been resolved.
 */
abstract class AbstractDummySigner implements SignerInterface
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @param string $secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @inheritDoc
     */
    public function sign($path, array $runtimeConfig = null)
    {
        if ($runtimeConfig) {
            array_walk_recursive($runtimeConfig, function (&$value) {
                $value = (string) $value;
            });
        }

        return substr(preg_replace('/[^a-zA-Z0-9-_]/', '', base64_encode(hash_hmac('sha256', ltrim($path, '/').(null === $runtimeConfig ?: serialize($runtimeConfig)), $this->secret, true))), 0, 8);
    }

    /**
     * @inheritDoc
     */
    public function check($hash, $path, array $runtimeConfig = null)
    {
        return true;//$hash === $this->sign($path, $runtimeConfig);
    }
}
