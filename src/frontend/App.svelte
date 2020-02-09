<!--suppress UnnecessaryLabelJS -->
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

    let currentPage = 0;
    let pageCapacity = 0;
    let taskCount = {};
    async function requestPaging(page, capacity) {
        window.$.ajax({
            type: "GET",
            url: `/api/v1/task/list/amount/`,
            dataType: "json",
            success: function (data) {
                currentPage = parseInt(page);
                pageCapacity = parseInt(capacity);
                taskCount = data;
            },
            error: function (jqXHR, textStatus, errorThrown) {
            },
            timeout: 500,
        });
    }
    let taskCollection = [];
    async function requestPage(page, capacity) {
        window.$.ajax({
            type: "GET",
            url: `/api/v1/task/list/${page}/${capacity}/`,
            dataType: "json",
            success: function(data) {
                taskCollection = data;
            },
            error: function (jqXHR, textStatus, errorThrown) {
            },
            timeout: 500,
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
        window.$.ajax({
            type: "GET",
            url: `/api/v1/task/search/${sample}/`,
            dataType: "json",
            success: function(data) {
                taskCount = 0;
                taskCollection = data;
            },
            error: function (jqXHR, textStatus, errorThrown) {
            },
            timeout: 500,
        });
    }

    function browseTask(event) {
        const taskId = event.detail.id;
        requestTask(taskId,renderTask);
    }

    async function requestTask(id,render) {
        window.$.ajax({
            type: "GET",
            url: `/api/v1/task/${id}/`,
            dataType: "json",
            success: render,
            error: function (jqXHR, textStatus, errorThrown) {
            },
            timeout: 500,
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

</script>
<svelte:body class:modal-open="{isModal}"/>
<div class="container">
    <h1>Трекер рабочих заданий</h1>
    <SearchForm on:search={applySample}></SearchForm>
    <TaskList on:show="{browseTask}"
    bind:taskCollection="{taskCollection}"></TaskList>
    <Paging bind:page={currentPage} bind:capacity={pageCapacity}
        bind:taskCount={taskCount} {settings}
        on:move="{browsePage}"></Paging>
    <DetailTaskView bind:isModal="{isModal}" {...task}></DetailTaskView>
</div>
<div class="{isModal ? 'modal-backdrop fade in' : ''}"></div>
