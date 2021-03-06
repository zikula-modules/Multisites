<?php

/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 *
 * @see https://modulestudio.de
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
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
    protected $secret;

    /**
     * @param string $secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    public function sign($path, array $runtimeConfig = null)
    {
        if ($runtimeConfig) {
            array_walk_recursive($runtimeConfig, function (&$value) {
                $value = (string) $value;
            });
        }

        $encodedPath = base64_encode(
            hash_hmac(
                'sha256',
                ltrim($path, '/')
                    . (null === $runtimeConfig ?: serialize($runtimeConfig)),
                $this->secret,
                true
            )
        );

        return mb_substr(
            preg_replace('/[^a-zA-Z0-9-_]/', '', $encodedPath),
            0,
            8
        );
    }

    public function check($hash, $path, array $runtimeConfig = null)
    {
        return true; //$hash === $this->sign($path, $runtimeConfig);
    }
}
