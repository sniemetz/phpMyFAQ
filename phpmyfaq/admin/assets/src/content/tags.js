/**
 * JavaScript functions for all tag administration stuff
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2014-2025 phpMyFAQ Team
 * @license   http://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2014-08-16
 */

import autocomplete from 'autocompleter';
import { addElement } from '../../../../assets/src/utils';
import { deleteTag, fetchTags } from '../api';
import { pushNotification } from '../utils/index.js';

export const handleTags = () => {
  const editTagButtons = document.querySelectorAll('.btn-edit');
  const deleteButtons = document.querySelectorAll('.btn-delete');
  const tagForm = document.getElementById('tag-form');
  const tagsAutocomplete = document.querySelector('.pmf-tags-autocomplete');

  if (editTagButtons) {
    editTagButtons.forEach((element) => {
      element.addEventListener('click', (event) => {
        const tagId = event.target.getAttribute('data-btn-id');
        const span = document.querySelector(`span[id="tag-id-${tagId}"]`);

        if (span) {
          span.replaceWith(
            addElement('input', {
              type: 'text',
              name: 'tag',
              value: span.innerText,
              classList: 'form-control',
              id: `tag-id-${tagId}`,
            })
          );
        } else {
          const input = document.querySelector(`input[id="tag-id-${tagId}"]`);
          input.replaceWith(
            addElement('span', {
              innerHTML: input.value.replace(/\//g, '&#x2F;'),
              id: `tag-id-${tagId}`,
            })
          );
        }
      });
    });
  }

  if (deleteButtons) {
    deleteButtons.forEach((element) => {
      element.addEventListener('click', async (event) => {
        const tagId = event.target.getAttribute('data-pmf-id');

        const response = await deleteTag(tagId);
        if (response.success) {
          pushNotification(response.success);
          const row = document.getElementById(`pmf-row-tag-id-${tagId}`);
          row.remove();
        } else {
          throw new Error('Network response was not ok: ' + JSON.stringify(response.error));
        }
      });
    });
  }

  if (tagForm) {
    tagForm.addEventListener('submit', (event) => {
      event.preventDefault();

      const input = document.querySelector('input:focus');
      const tagId = input.getAttribute('id').replace('tag-id-', '');
      const tag = input.value;
      const csrf = document.querySelector('input[name=pmf-csrf-token]').value;

      fetch('./api/content/tag', {
        method: 'PUT',
        headers: {
          Accept: 'application/json, text/plain, */*',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          csrf: csrf,
          id: tagId,
          tag: tag,
        }),
      })
        .then(async (response) => {
          if (response.ok) {
            return response.json();
          }
          throw new Error('Network response was not ok: ', { cause: { response } });
        })
        .then(() => {
          input.replaceWith(
            addElement(
              'span',
              {
                innerHTML: input.value.replace(/\//g, '&#x2F;'),
                id: `tag-id-${tagId}`,
              },
              [
                addElement('span', {
                  classList: 'ms-1 badge rounded-pill bg-success',
                  innerHTML: '✓',
                }),
              ]
            )
          );
        })
        .catch(async (error) => {
          const errorMessage = await error.cause.response.json();
          const table = document.querySelector('.table');
          table.insertAdjacentElement(
            'beforebegin',
            addElement('div', { classList: 'alert alert-danger', innerText: errorMessage })
          );
        });
    });
  }

  if (tagsAutocomplete) {
    autocomplete({
      input: tagsAutocomplete,
      minLength: 1,
      onSelect: async (item, input) => {
        let currentTags = input.value;
        let currentTagsArray = currentTags.split(',');
        if (currentTags.length === 0) {
          currentTags = item.tagName;
        } else {
          currentTagsArray[currentTagsArray.length - 1] = item.tagName;
          currentTags = currentTagsArray.join(',');
        }
        input.value = currentTags;
      },
      fetch: async (text, callback) => {
        let match = text.toLowerCase();
        const tags = await fetchTags(match);
        callback(
          tags.filter((tag) => {
            const lastCommaIndex = match.lastIndexOf(',');
            match = match.substring(lastCommaIndex + 1);
            return tag.tagName.toLowerCase().indexOf(match) !== -1;
          })
        );
      },
      render: (item, value) => {
        const regex = new RegExp(value, 'gi');
        return addElement('div', {
          classList: 'pmf-tag-list-result border',
          innerHTML: item.tagName,
        });
      },
      emptyMsg: 'No tags found.',
    });
  }
};
