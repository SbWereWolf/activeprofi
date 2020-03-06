<script>
    import {onMount} from 'svelte';

    import DetailTaskView from './DetailTaskView.svelte';
    import Paging from './Paging.svelte';
    import SearchForm from './SearchForm.svelte';
    import TaskList from './TaskList.svelte';

    const settings = {
        capacity: 10,
        pagesBefore: 5,
        pagesAfter: 5,
        placesGap: 2,
    };

    function browsePage(event) {
        const pageIndex = event.detail.index;
        renderPage(pageIndex);
    }
    onMount(() => {
        renderPage();
    });

    function renderPage(index = 0) {
        requestPaging(index, settings.capacity);
        requestPage(index, settings.capacity);
    }

    const headers = new Headers(
        {"Content-Type": "application/json; charset=utf-8",});

    let currentPage = 0;
    let pageCapacity = 0;
    let taskCount = {};
    async function requestPaging(page, capacity) {
        const request = new Request('/api/v1/task/list/amount/', {
          method: 'GET',
          headers: headers,
        });

        fetch(request)
        .then((response) => response.json())
        .then((json) => {
            currentPage = parseInt(page);
            pageCapacity = parseInt(capacity);
            taskCount = json;
        });
    }
    let taskCollection = [];
    async function requestPage(page, capacity) {
        const request = new Request(
            `/api/v1/task/list/${page}/${capacity}/`, {
          method: 'GET',
          headers: headers,
        });

        fetch(request)
        .then((response) => response.json())
        .then((json) => {
            taskCollection = json;
        });
    }

    async function applySample(event) {
        const sample = event.detail.sample;
        if(sample){
            search(sample);
        }
        if(!sample){
            renderPage();
        }
    }
    async function search(sample) {
        const request = new Request(
            `/api/v1/task/search/${sample}/`, {
          method: 'GET',
          headers: headers,
        });

        fetch(request)
        .then((response) => response.json())
        .then((json) => {
            taskCollection = json;
            taskCount = 0;
        });
    }

    function browseTask(event) {
        const taskId = event.detail.id;
        requestTask(taskId,renderTask);
    }

    async function requestTask(id,render) {
        const request = new Request(
            `/api/v1/task/${id}/`, {
          method: 'GET',
          headers: headers,
        });

        fetch(request)
        .then((response) => response.json())
        .then((json) => {
            render(json);
        });
    }

    let task = {};
    let isModal = false;

    function renderTask(data) {
        const isSuccess = data.hasOwnProperty(0);
        if (isSuccess) {
            isModal = true;
            task = data[0];
        }
    }

    import {fade} from 'svelte/transition';

</script>
<div class="container">
    <h1>Трекер рабочих заданий</h1>
    <SearchForm on:search={applySample}/>
    <TaskList on:show="{browseTask}"
              bind:taskCollection="{taskCollection}"/>
    <Paging bind:page={currentPage} bind:capacity={pageCapacity}
            bind:taskCount={taskCount} {settings}
            on:move="{browsePage}"/>
</div>
{#if isModal}
    <div transition:fade>
        <DetailTaskView bind:isModal="{isModal}" {...task}/>
    </div>
{/if}
