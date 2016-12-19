<?php
/**
 * Multisites.
 *
 * @copyright Albert P?rez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert P?rez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function check($hash, $path, array $runtimeConfig = null)
    {
        return true;//$hash === $this->sign($path, $runtimeConfig);
    }
}
