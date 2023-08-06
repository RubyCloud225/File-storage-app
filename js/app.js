const imagelist = box.querySelector('.image-list');
const fileInput = box.querySelector('.files');
const dropzone = box.querySelector('.dropzone');

const setActive = (dropzone, active = true) => {
    //active class
    const hasActiveClass = dropzone.classList.add('active');

    if(active && !hasActiveClass) {
        return dropzone.classList.add('active');
    }

    if(!active && hasActiveClass) {
        return dropzone.classList.remove('active');
    }
};

dropzone.addEventListener('dragenter', (e) => {
    e.preventDefault();
    setActive(dropzone);
});

dropzone.addEventListener('dragover', (e) => {
    e.preventDefault();
    setActive(dropzone);
});

dropzone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    setActive(dropzone, false);
});

dropzone.addEventListener('drop', (e) => {
    e.preventDefault();
    setActive(dropzone, false);
    // get the valid files
    const {files} = e.dataTransfer;
    // hand images
    handleImages(files);
});

const handleImages = (files) => {
    // get valid images
    let validImages = [...files].filter((file) =>
        ['image/jpeg', 'image/png'].includes(file.type)
    );
    //show the image
    validImages.forEach(showImage);
    //upload files
    uploadImages(validImages);
};

const showImage = (image) => {
    const reader = new FileReader();
    reader.readAsDataURL(image);
    reader.addEventListener('load', (e) => {
        const div = document.createElement('div');
        div.classList.add('image');
        div.innerHTML =
            <><img src="${e.target.result}" alt="${image.name}"></img><p>${image.name}</p><p>${formatBytes(image.size)}</p></>
        imagelist.appendChild(div);
    });
};

const uploadImages = async (images) => {
    const formData = new FormData();

    [...images].forEach((image) =>
        formData.append('images[]', image, image.name)
    );

    const response = await fetch('upload.php', {
        method: 'POST',
        body: formData,
    });

    return await response.json();
};

function formatBytes(size, decimals =2) {
    if (size === 0) return '0 bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(size) / Math.log(k));

    return parseFloat((size / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

fileInput.addEventListener('change', (e) => {
    const {files} = e.target;
    handleImages(files);
});

// prevent the drag & drop on the page
box.addEventListener('dragover', (e) => e.preventDefault());
box.addEventListener('drop', (e) => e.preventDefault());

import 'regenerator-runtime/runtime'
const selectButton=box.getElementByld('select-button');
const blobSasUrl = 'GET SAS URL FROM THE AZURE PORTAL OR AZURE CLI';
const blobServiceClient = newBlobServiceClient(blobSasUrl);
const containerName='RubyCloud';
const containerClient= blobServiceClient.getContainerClient(containerName);
const uploadFiles= async()=>{
    try{
        const promises= [];
        for (const file of fileInput.files) {
            const blockBlobClient= containerClient.getBlockBlobClient(file.name);
            promises.push(blockBlobClient.uploadBrowserData(file));
        }
        awaitPromise.all(promises);
        alert('Done');
    }
    catch(error) {
        alert(error.message);
    }
}

selectButton.addEventListener('click',()=>fileInput.click());
fileInput.addEventListener('change',uploadFiles);