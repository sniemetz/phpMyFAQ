/**
 * Comment functionality
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2016-2023 phpMyFAQ Team
 * @license   http://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2016-01-11
 */


 

export const expandSearchForm = () => {
    /// add classes instead of setting styles

    const reset = function (e) {
        var a = (e.type == "mousedown" &&  e.target.localName != "button"&& e.target.localName != "input");
        var b = (e.type == "keydown" && e.code == "Escape");
        if (a||b) { 

            document.querySelectorAll("ul.nav").forEach((o) => {
                o.style.visibility = "initial";
            });
            document.querySelectorAll("div.search form i, button.searchbar-submit").forEach((o) => {
                o.style.visibility = "hidden";
            });
            document.querySelector('button').blur();
            document.querySelector('input').blur();
            document.querySelectorAll("div.topmenu,form#search button.searchclosed").forEach((o) => {
                o.style.visibility = "visible";
            });
            document.querySelectorAll("div.searchcontainer, div.search").forEach((o) => {
                o.style.width = "";
            });
        
            document.removeEventListener('keydown', reset);
            document.removeEventListener('click', reset);
        }
    }

    const expand = function (e) {
        console.log(e)

        document.querySelectorAll("div.topmenu,form#search button.searchclosed").forEach((o) => {
            o.style.visibility = "hidden";
        });
        document.querySelectorAll("div.searchcontainer, div.search").forEach((o) => {
            o.style.width = "100%";
        });
    
        document.querySelectorAll("div.search form i, button.searchbar-submit").forEach((o) => {
            o.style.visibility = "visible";
        });

        
        e.stopPropagation();
    
        document.addEventListener('keydown', reset);
        document.addEventListener('mousedown', reset);

    }
    
    document.querySelector('form#search button.searchclosed').addEventListener('mousedown', expand);

}


