document.addEventListener('DOMContentLoaded', async () => {
    const createButton = document.getElementById('create');

    await fetchClips();

    if (createButton) {
        createButton.addEventListener('click', async (event) => {
            event.preventDefault(); // Prevent default behavior

            const clipName = document.getElementById('cname').value.trim();
            const clipContent = document.getElementById('clip-input-new').value.trim();

            try {
                document.getElementById('clip-input-new').classList.contains('editing')
                ? await updateClip(clipName, clipContent)
                : await createClip(clipName, clipContent);
            } catch (error) {
                console.error('An error occurred:', error);
            }
        });
    } else {
        console.error('Create button not found in the DOM.');
    }
});

async function createClip(name, content) {

    if(content.trim().length === 0) {
        showInfo('Clip content cannot be empty!', 'warning');
        return;
    };

    if(name.trim().length === 0) {
        showInfo('Clip name cannot be empty!', 'warning');
        return;
    };

    if(Array.from(document.getElementsByClassName('clip'))
            .some(el => el.innerText === name))  {
        showInfo('Clip already exists!', 'warning');
        return;
    };

    try {
        const response = await fetch('http://192.168.0.121/api/clipboard.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: name,
                content: btoa(content),
                action: 'create'
            }),
        });

        const data = await response.json();

        if (response.ok && data.status) {
            showInfo('Clip created successfully!');
            await fetchClips();
        } else {
            showInfo('Clip creation failed!', 'error');
            console.error('Clip creation failed:', data.error || 'Unknown error');
        }
    } catch (error) {
        console.error('An error occurred while creating clip:', error);
        alert('A network error occurred. Please try again later.');
    }
}

async function fetchClips() {
    try {
        const response = await fetch('http://192.168.0.121/api/clipboard.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'fetchall'
            }),
        });

        const data = await response.json();

        if(data.clipcount == 0) {
            document.getElementById('cname').value = "";
            document.getElementById('clip-input-new').value = "";
            document.getElementById('callclips').innerHTML = "";
            exitEditingMode();
            exitSelectedMode();
            return;
        }

        if (response.ok && data.clips) {
            document.getElementById('cname').value = "";
            document.getElementById('clip-input-new').value = "";
            document.getElementById('callclips').innerHTML = "";
            const clipscontainer = document.getElementById('callclips')

            data.clips.forEach(clip => {
                var newclip = document.createElement('div');
                newclip.className = "clip";
                newclip.innerText = clip.name
                clipscontainer.appendChild(newclip);
            });
            setClipPreviewEventListener();
        } else {
            console.error('Clips fetch failed:', data.error || 'Unknown error');
        }
    } catch (error) {
        console.error('An error occurred while fetching clips:', error);
        alert('A network error occurred. Please try again later.');
    }
}

async function fetchClip(name) {
    try {
        const response = await fetch('http://192.168.0.121/api/clipboard.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: name,
                action: 'fetch'
            }),
        });

        const data = await response.json();

        if(data.clipcount == 0) return;

        if (response.ok && data.content) {
            document.getElementById('clip-input-new').value = atob(data.content);
        } else {
            console.error('Clips fetch failed:', data.error || 'Unknown error');
        }
    } catch (error) {
        console.error('An error occurred while fetching clips:', error);
        alert('A network error occurred. Please try again later.');
    }
}

async function updateClip(name, content) {
    try {
        const response = await fetch('http://192.168.0.121/api/clipboard.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: name,
                content: btoa(content),
                action: 'update'
            }),
        });

        const data = await response.json();

        if (response.ok && data.status) {
            showInfo('Clip updated successfully!');
            await fetchClips();
            exitEditingMode();
            exitSelectedMode();
        } else {
            showInfo('Clip update failed!', 'error');
            console.error('Clip update failed:', data.error || 'Unknown error');
        }
    } catch (error) {
        console.error('An error occurred while updating clip:', error);
        alert('A network error occurred. Please try again later.');
    }
}

async function deleteClip() {

    const name = document.getElementsByClassName('selected')[0].innerText;

    try {
        const response = await fetch('http://192.168.0.121/api/clipboard.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: name,
                action: 'delete'
            }),
        });

        const data = await response.json();

        if (response.ok && data.status) {
            showInfo('Clip deleted successfully!');
            await fetchClips();
            exitSelectedMode();
        } else {
            showInfo('Clip deletion failed!');
            console.error('Clip deletion failed:', data.error || 'Unknown error');
        }
    } catch (error) {
        console.error('An error occurred while deleting clip:', error);
        alert('A network error occurred. Please try again later.');
    }
}

function copyClip() {
    const element = document.getElementById('clip-input-new');
    if (element) {
        element.select();
        element.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(element.value);
    }
    showInfo('Clip content copied to clipboard!')
}

function editClip() {
    document.getElementById('clip-input-new').classList.contains('editing')
        ? exitEditingMode()
        : enterEditingMode();
}

async function setClipPreviewEventListener() {

    const clips = document.getElementsByClassName('clip');

    for (const clip of clips) {
        clip.addEventListener('click', async (event) => {

            document.getElementById('clip-input-new').value = "";
            document.getElementById('cname').value = "";

            if(event.target.classList.contains('selected')) {
                event.target.style.border = "3px solid transparent";
                event.target.classList.remove('selected');
                exitSelectedMode();
            } else {
                for(const clip of document.getElementsByClassName('clip')) {
                    clip.style.border = '3px solid transparent';
                    clip.classList.remove('selected');
                }

                const name = event.target.innerText;

                try {
                    await fetchClip(name); 
                    document.getElementById('cname').value = name; 
                    event.target.classList.add('selected');
                    event.target.style.border = "3px solid #00efef" 
                    enterSelectedMode();
                } catch (error) {
                    console.error('Error handling clip click:', error);
                }
            }
        });
    }
}

function enterSelectedMode() {
    for(const action of document.getElementsByClassName('action')) {
        action.style.display = 'flex'
    }

    document.getElementById('cname').disabled = true;
    document.getElementById('clip-input-new').disabled = true;
    document.getElementById('create').style.display = "none";
}

function exitSelectedMode() {

    for(const action of document.getElementsByClassName('action')) {
        action.style.display = 'none'
    }

    for(const clip of document.getElementsByClassName('clip')) {
        clip.style.border = '3px solid transparent'
    }

    document.getElementById('cname').disabled = false;
    document.getElementById('clip-input-new').disabled = false;
    document.getElementById('create').style.display = "flex";
}

function enterEditingMode() {
    for(const clip of document.getElementsByClassName('clip')) {
        clip.style.pointerEvents = 'none'
    }

    document.getElementById('clip-input-new').classList.add('editing');
    document.getElementById('clip-input-new').disabled = false;
    document.getElementById('create').innerText = "Update Clip"
    document.getElementById('copy').style.display = "none";
    document.getElementById('create').style.display = "flex";
    document.getElementById('trash').style.display = "none";
    document.getElementById('edit').style.border = "3px solid #00efef";
    document.getElementById('cnewclip').childNodes[1].innerText = "Edit Clip"
}

function exitEditingMode() {
    for(const clip of document.getElementsByClassName('clip')) {
        clip.style.pointerEvents = 'all'
    }

    document.getElementById('clip-input-new').classList.remove('editing');
    document.getElementById('clip-input-new').disabled = true;
    document.getElementById('create').style.display = "none";
    document.getElementById('create').innerText = "Create Clip"
    document.getElementById('copy').style.display = "flex";
    document.getElementById('trash').style.display = "flex";
    document.getElementById('edit').style.border = "3px solid transparent";
    document.getElementById('cnewclip').childNodes[1].innerText = "Create Clip"
}

function showInfo(infoText, level) {
    document.getElementById('infobox').innerText = infoText;
    document.getElementById('infobox').classList.remove('hidden');
    document.getElementById('infobox').classList.add('visible');

    switch(level) {
        case 'success':
            document.getElementById('infobox').style.color = 'lightgreen'
            break;
        case 'warning':
            document.getElementById('infobox').style.color = 'orange'
            break;
        case 'error':
            document.getElementById('infobox').style.color = 'red'
            break;
        default:
            document.getElementById('infobox').style.color = 'lightgreen'
      } 

    setTimeout(() => {
        document.getElementById('infobox').classList.remove('visible');
        document.getElementById('infobox').classList.add('hidden');
    }, 5000);
}
