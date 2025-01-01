/**
 * phpMyFAQ admin open questions
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ
 * @author    Thorsten Rinne
 * @copyright 2023-2025 phpMyFAQ Team
 * @license   http://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2023-03-04
 */

import { addElement, serialize } from '../../../../assets/src/utils';
import { toggleQuestionVisibility } from '../api/index.js';
import { pushErrorNotification } from '../utils/index.js';

export const handleOpenQuestions = () => {
  const deleteButton = document.getElementById('pmf-delete-questions');

  if (deleteButton) {
    deleteButton.addEventListener('click', (event) => {
      event.preventDefault();

      const responseMessage = document.getElementById('returnMessage');
      const form = document.querySelector('#phpmyfaq-open-questions');
      const questions = new FormData(form);

      responseMessage.innerHTML = '';
      fetch('./api/question/delete', {
        method: 'POST',
        headers: {
          Accept: 'application/json, text/plain, */*',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          data: serialize(questions),
        }),
      })
        .then(async (response) => {
          if (response.ok) {
            return response.json();
          }
          throw new Error('Network response was not ok: ', { cause: { response } });
        })
        .then((response) => {
          if (response.success) {
            responseMessage.append(
              addElement('div', { classList: 'alert alert-success', innerText: response.success })
            );
            const questionsToDelete = document.querySelectorAll('tr td input:checked');
            questionsToDelete.forEach((toDelete) => {
              toDelete.parentNode.parentNode.parentNode.remove();
            });
          } else {
            responseMessage.append(addElement('div', { classList: 'alert alert-danger', innerText: response.error }));
          }
        })
        .catch(async (error) => {
          const errorMessage = await error.cause.response.json();
          responseMessage.append(addElement('div', { classList: 'alert alert-danger', innerText: errorMessage }));
        });
    });
  }
};

export const handleToggleVisibility = () => {
  const toggleVisibility = document.querySelectorAll('.pmf-toggle-visibility');

  if (toggleVisibility) {
    toggleVisibility.forEach((element) => {
      element.addEventListener('click', async (event) => {
        event.preventDefault();

        const questionId = element.getAttribute('data-pmf-question-id');
        const visibility = element.getAttribute('data-pmf-visibility');
        const csrfToken = element.getAttribute('data-pmf-csrf');

        const response = await toggleQuestionVisibility(questionId, visibility, csrfToken);

        if (response.success) {
          element.innerText = response.success;
        } else {
          pushErrorNotification(response.error);
        }

        console.log(questionId, visibility, csrfToken);
      });
    });
  }
};
