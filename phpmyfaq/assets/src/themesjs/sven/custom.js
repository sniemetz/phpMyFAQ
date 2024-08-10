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
    const doReset = function () {
        document.querySelectorAll("ul.nav").forEach((o) => {
            o.style.visibility = "initial";
        });
        document.querySelectorAll("div.search form i").forEach((o) => {
            o.style.visibility = "hidden";
        });
        document.querySelector('form#search .searchbar-submit').blur();
        document.querySelector('form#search .searchclosed').blur();

        document.querySelectorAll("div.topmenu").forEach((o) => {
            o.style.visibility = "visible";
        });
        document.querySelectorAll("div.searchcontainer, div.search").forEach((o) => {
            o.style.width = "";
        });
    
        document.removeEventListener('keydown', reset);
        document.removeEventListener('click', reset);

    }
    const reset = function (e) {
        var a = (e.type == "mousedown" &&  e.target.localName != "button"&& e.target.localName != "input");
        var b = (e.type == "keydown" && e.code == "Escape");
        if (a||b) { 
            doReset();
        }
    }
    const chk = function (e) {
        if (e.target.value != "") {
            document.querySelector('form#search button.searchbar-submit').style.visibility = "visible";
            document.querySelector('form#search button.searchclosed').style.visibility = "hidden";
        } else {
            document.querySelector('form#search button.searchclosed').style.visibility = "visible";
            document.querySelector('form#search button.searchbar-submit').style.visibility = "hidden";
        }

    }

    const expand = function (e) {
        e.stopPropagation();
        if ( document.querySelector("div.topmenu").style.visibility == 'hidden' ) {
            if ( document.querySelector('form#search input.searchbar-input').value == "" ) {
                doReset();
            }
            return;
        }
        doExpand();
    }

    const doExpand = function () {
        document.querySelectorAll("div.topmenu").forEach((o) => {
            o.style.visibility = "hidden";
        });
        document.querySelectorAll("div.searchcontainer, div.search").forEach((o) => {
            o.style.width = "100%";
        });
    
        document.querySelectorAll("div.search form").forEach((o) => {
            o.style.visibility = "visible";
        });

        
        
        document.querySelector('form#search input.searchbar-input').addEventListener('keyup', chk);
    
        document.addEventListener('keydown', reset);
        document.addEventListener('mousedown', reset);

    }
    
    document.querySelector('form#search button.searchclosed').addEventListener('mousedown', expand);

}


