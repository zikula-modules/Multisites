<?php
/**
 * Multisites.
 *
 * @copyright Albert Pérez Monfort (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package Multisites
 * @author Albert Pérez Monfort <aperezm@xtec.cat>.
 * @link http://modulestudio.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

/**
 * Event handler base class for mailing events.
 */
class Multisites_Listener_Base_Mailer
{
    /**
     * Listener for the `module.mailer.api.sendmessage` event.
     *
     * Invoked from `Mailer_Api_User#sendmessage`.
     * Subject is `Mailer_Api_User` with `$args`.
     * This is a notifyUntil event so the event must `$event->stop()` and set any
     * return data into `$event->data`, or `$event->setData()`.
     *
     * @param Zikula_Event $event The event instance.
     */
    public static function sendMessage(Zikula_Event $event)
    {
    }
}
