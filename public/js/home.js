document.addEventListener('DOMContentLoaded', () => {
    // Add new entries to the DOM
    function addEntries(data) {
        const noEntries = entriesContainer.textContent.includes('No entries');

        let html = '';

        // Check for no entries
        if (noEntries) {
            entriesContainer.innerHTML = '';
        }

        if (data.length) {
            data.forEach((entry, index) => {
                const isLast = index === data.length - 1;
                const requestPath = String(entry.request.path).split('?')[0];

                html += `<div id="entry-${entry.id}" class="entry">`;

                if (entry.terminated) {
                    html += '<div class="terminated">';
                } else {
                    html += '<div class="line">';
                }

                html += `<div class="datetime"><pre class="datetime">${entry.datetime}</pre>`;

                html += `<div class="request-icon" data-id="${entry.id}">${requestIcon}</div>`;

                if (entry.xdebug) {
                    html += `<div class="disabled-icon" data-id="${entry.id}">${traceIcon}</div>`;
                } else {
                    html += `<div class="trace-icon" data-id="${entry.id}">${traceIcon}</div>`;
                }

                html += `<div class="delete-icon" data-id="${entry.id}">${deleteIcon}</div>`;

                if (entry.terminated) {
                    html += '</div><pre>terminated</pre></div>';
                } else {
                    html += '</div></div>';
                }

                html += `<div class="line"><pre>${entry.request.method} ${requestPath}</pre></div>`;

                if (entry.request.method === 'GET') {
                    html += `<div id="request-${entry.id}" class="request"><pre>` + JSON.stringify(entry.request.get, null, 4) + '</pre></div>';
                } else if (entry.request.method === 'POST') {
                    html += `<div id="request-${entry.id}" class="request"><pre>` + JSON.stringify(entry.request.post, null, 4) + '</pre></div>';
                }

                if (entry.xdebug) {
                    const xdebugBasename = entry.xdebug.split('/').pop();
                    html += `<div class="line"><pre>Xdebug trace</pre></div>`;
                    html += `<div class="line"><pre><a class="xdebug" href="/xdebug?id=${entry.id}" target="_blank">${xdebugBasename}</a></pre></div>`;
                } else {
                    if (entry.class && entry.function) {
                        html += `<div class="line"><pre>${entry.class} → ${entry.function}()</pre></div>`;
                    }

                    html += `<div class="line"><pre>${entry.file}:${entry.line}</pre></div>`;
                    if (entry.comment) html += `<div class="line"><pre class="italic">${entry.comment}</pre></div>`;
                    html += `<div id="trace-${entry.id}" class="trace"><pre>` + entry.trace + '</pre></div>';
                    html += '<div class="vardump">' + atob(entry.dump) + '</div>';
                }

                html += `<hr>`;
                html += `</div>`;
            });
        }

        showNotice();

        entriesContainer.insertAdjacentHTML('beforeend', html);

        runScriptsFrom(entriesContainer);
    }

    // Remove entries from the DOM
    function removeEntries(data) {
        if (data.length) {
            data.forEach((id) => {
                const element = document.getElementById(`entry-${id}`);

                if (element) {
                    element.remove();
                }
            });
        }
    }

    // Run scripts in VarDumper output
    function runScriptsFrom(element) {
        element.querySelectorAll('script').forEach(script => {
            const s = document.createElement('script');
            if (script.src) s.src = script.src;
            else s.textContent = script.textContent;
            document.body.appendChild(s);
            script.remove();
        });
    }

    // Show notice to the side of the theme buttons
    function showNotice() {
        const notifyElements = [notify1, notify2];

        notifyElements.forEach(el => {
            // Make visible
            el.classList.add('on');
            el.style.opacity = '1';
            el.style.transition = 'opacity 1.0s ease';

            // Fade out after 2 seconds
            setTimeout(() => {
                el.style.opacity = '0';

                // Remove class after fade completes
                setTimeout(() => {
                    el.classList.remove('on');
                }, 1000); // matches transition duration
            }, 2000);
        });
    }

    // Style auto button
    function styleAutoButton(state) {
        if (state === 'running') {
            autoBtn.classList.add("running");
            autoBtn.classList.remove("paused");
        } else {
            autoBtn.classList.add("paused");
            autoBtn.classList.remove("running");
        }
    }

    const debugToken = document.getElementById('debugToken').value;
    const debugPollingInterval = (+document.getElementById('debugPollingInterval').value) || 1000;

    const entriesContainer = document.getElementById("entries-container");
    const notify1 = document.getElementById("notify-1");
    const notify2 = document.getElementById("notify-2");

    const autoBtn = document.getElementById("auto-button");
    const clearBtn = document.getElementById("clear-button");

    let currentState = localStorage.getItem("currentState") ?? 'running';

    let previousLogIdsValue = document.getElementById('logIds').value === '' ? null : document.getElementById('logIds').value;
    let previousLogIds = previousLogIdsValue === null ? [] : previousLogIdsValue.split(",").map(Number);

    const requestIcon = '<svg fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M4.857 3A1.857 1.857 0 0 0 3 4.857v4.286C3 10.169 3.831 11 4.857 11h4.286A1.857 1.857 0 0 0 11 9.143V4.857A1.857 1.857 0 0 0 9.143 3H4.857Zm10 0A1.857 1.857 0 0 0 13 4.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 21 9.143V4.857A1.857 1.857 0 0 0 19.143 3h-4.286Zm-10 10A1.857 1.857 0 0 0 3 14.857v4.286C3 20.169 3.831 21 4.857 21h4.286A1.857 1.857 0 0 0 11 19.143v-4.286A1.857 1.857 0 0 0 9.143 13H4.857Zm10 0A1.857 1.857 0 0 0 13 14.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 21 19.143v-4.286A1.857 1.857 0 0 0 19.143 13h-4.286Z" clip-rule="evenodd"/></svg>';
    const traceIcon = '<svg fill="none" viewBox="4 2 16 20"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M9 8h10M9 12h10M9 16h10M4.99 8H5m-.02 4h.01m0 4H5"/></svg>';
    const deleteIcon = '<svg fill="none" viewBox="4 2 16 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"></path></svg>';

    autoBtn.addEventListener("click", () => {
        currentState = currentState === 'running' ? 'paused' : 'running';
        localStorage.setItem("currentState", currentState);
        styleAutoButton(currentState);
    });

    clearBtn.addEventListener("click", () => {
        try {
            fetch('/api/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-DEBUG-TOKEN': debugToken
                },
            });

            entriesContainer.innerHTML = '<pre>No entries.</pre>';
        } catch (error) {
            console.error("Request failed:", error);
        }
    });

    // Stops Chrome's triple-click selection on the trace and delete icons
    document.addEventListener('pointerdown', function (e) {
        if (e.target.closest('div.request-icon')) {
            e.preventDefault();
        } else if (e.target.closest('div.trace-icon')) {
            e.preventDefault();
        } else if (e.target.closest('div.delete-icon')) {
            e.preventDefault();
        } else if (e.target.closest('div.disabled-icon')) {
            e.preventDefault();
        }
    });

    document.addEventListener('click', function(e) {
        const el = e.target.closest('div.delete-icon');

        if (el) {
            const entryId = el.dataset.id;

            // Remove deleted log ID
            previousLogIds = previousLogIds.filter(x => x !== Number(entryId));

            const entryDiv = document.getElementById(`entry-${entryId}`)

            if (entryDiv) entryDiv.remove();

            let entriesCount = document.querySelectorAll('div.entry').length;

            if (entriesCount === 0) {
                entriesContainer.innerHTML = '<pre>No entries.</pre>';
            }

            try {
                fetch('/api/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-DEBUG-TOKEN': debugToken
                    },
                    body: JSON.stringify({
                        entryId: entryId
                    })
                });
            } catch (error) {
                console.error("Request failed:", error);
            }
        }
    });

    document.addEventListener('click', function(e) {
        const el = e.target.closest('div.request-icon');

        if (el) {
            const entryId = el.dataset.id;

            const requestDiv = document.getElementById(`request-${entryId}`);

            if (requestDiv) {
                requestDiv.style.display = window.getComputedStyle(requestDiv).display === 'none' ? 'block' : 'none';
            }

        }
    });

    document.addEventListener('click', function(e) {
        const el = e.target.closest('div.trace-icon');

        if (el) {
            const entryId = el.dataset.id;

            const traceDiv = document.getElementById(`trace-${entryId}`);

            if (traceDiv) {
                traceDiv.style.display = window.getComputedStyle(traceDiv).display === 'none' ? 'block' : 'none';
            }

        }
    });

    setInterval(async () => {
        if (currentState === 'paused') {
            return;
        }

        try {
            let response = await fetch('/api/ids', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-DEBUG-TOKEN': debugToken,
                },
            });

            let responseData = await response.json();
            let logData = responseData.data;

            const logIdsDeleted = previousLogIds.filter(x => !logData.includes(x));

            const logIdsNew = logData.filter(x => !previousLogIds.includes(x));

            if (logIdsDeleted.length) {
                // Remove deleted log IDs
                previousLogIds = previousLogIds.filter(x => !logIdsDeleted.includes(x));

                // Remove entries
                removeEntries(logIdsDeleted);
            }

            if (logIdsNew.length) {
                // Append new log IDs
                previousLogIds.push(...logIdsNew);

                let response = await fetch('/api/entries', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-DEBUG-TOKEN': debugToken
                    },
                    body: JSON.stringify({
                        logIds: logIdsNew
                    })
                });

                let responseData = await response.json();

                await addEntries(responseData.data);
            }
        } catch (error) {
            console.error("Request failed:", error);
        }
    }, debugPollingInterval);

    styleAutoButton(currentState);
});
