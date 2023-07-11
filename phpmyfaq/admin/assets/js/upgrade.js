/**
 * Upgrade related code.
 *
 * - Code for checking for updates.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ
 * @author    Thorsten Rinne
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2022-2023 phpMyFAQ Team
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2023-07-11
 */

function checkForUpdates() {
    const button = document.getElementById('check-updates');
    if (button) {
        fetch('index.php?action=ajax&ajax=updates&ajaxaction=check-updates', {
            method: 'POST',
            headers: {
                Accept: 'application/json, text/plain, */*',
                'Content-Type': 'application/json'
            }
        })
                .then(async (response) => {
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error('Network response was not ok: ', {cause: {response}});
                })
                .then((response) => {
                    if (response.version === 'current') {
                        const element = document.createElement('p');
                        element.textContent = response.message;
                        button.after(element);
                    } else {
                        const element = document.createElement('p');
                        element.textContent = response.message;
                        button.after(element);
                    }
                });
    }
}
