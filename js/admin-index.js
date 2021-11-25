'use strict';

document.querySelector('#search_wrapper button[type="submit"]')
    .addEventListener('click', (e) => {
        e.preventDefault();
        fetchId();
    });

document.querySelector('#search_output button')
    .addEventListener('click', (e) => {
        e.preventDefault();
        output({mode: 'clear'});
    })

function fetchId() {
    let input = document.querySelector('#search_wrapper input');
    let req = new Request(`/api/${input.value}`);

    fetch(req)
        .then(res => {
            if (!res.ok) throw res;

            return res.json();
        })
        .then(({ data }) => {
            let d = {
                id: data.id,
                type: data.type,
                ...data.attributes
            };

            d = JSON.stringify(d, undefined, 4);
            output({data: syntaxHighlight(d)});
        })
        .catch(e => {
            output({data: e.statusText, mode: 'error'});
        });
}

function output({ data, mode }) {
    let pre = document.querySelector('#search_output > pre');

    if (mode == 'clear') {
        pre.innerHTML = '';
        pre.classList.remove('clear-btn');
    } else {
        pre.innerHTML = data;

        if (mode == 'error') {
            pre.classList.remove('clear-btn');
            return;
        }

        pre.classList.add('clear-btn');
    }
}

function syntaxHighlight(json) {
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}
