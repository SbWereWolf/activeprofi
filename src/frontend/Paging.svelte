<script>
    import {createEventDispatcher} from 'svelte';
    import OrdinalPlace from './OrdinalPlace.svelte';

    export let page = 0;
    export let capacity = 0;
    export let taskCount = {};
    export let settings = {
        capacity: 10,
        pagesBefore: 5,
        pagesAfter: 5,
        placesGap: 2,
    };

    const forCurrent = "bg-success";

    let roundLimit;
    let pagingPlaces;
    {
        let isSuccess = taskCount.hasOwnProperty(0);
        let element;
        if (isSuccess) {
            element = taskCount[0];
            isSuccess = element.hasOwnProperty("amount");
        }
        let amount = 0;
        if (isSuccess) {
            amount = element.amount;
        }

        roundLimit = Math.ceil(amount / capacity);
        isSuccess = typeof roundLimit !== typeof undefined
            && !isNaN(roundLimit);
        if(isSuccess){
            isSuccess = roundLimit > 0;
        }

        let toStart = false;
        let toFinish = false;
        if (isSuccess) {
            toStart = (page - settings.placesGap
                - settings.pagesBefore) > 0;
            toFinish = (page + settings.placesGap
                + settings.pagesAfter) < roundLimit - 1;
        }

        pagingPlaces = [];
        const marked = [];

        if (isSuccess && toStart) {
            pagingPlaces = addPlace(pagingPlaces, 0);
            pagingPlaces = addPlace(pagingPlaces,
                Number.MIN_SAFE_INTEGER, "..");
            marked.push(0);
            marked.push(1);
        }
        let pageOccur = false;
        if (isSuccess && !toStart) {
            for (let index = 0; index < 2; index++) {
                const addLink = (index < roundLimit)
                    && !marked.includes(index);
                if (addLink && page !== index) {
                    pagingPlaces=addPlace(pagingPlaces, index);
                    marked.push(index);
                }
                if (addLink && page === index
                    && !marked.includes(index)) {

                    pagingPlaces =
                        addPlace(pagingPlaces, page, "", forCurrent);
                    pageOccur = true;
                    marked.push(page);
                }
            }
        }

        if (isSuccess) {
            for (let index = 0; index < settings.pagesBefore; index++)
            {
                const currentPage = index - settings.pagesBefore
                    + page;
                const addLink = (currentPage < roundLimit)
                    && (currentPage > -1)
                    && !marked.includes(currentPage);
                if (addLink) {
                    pagingPlaces=
                        addPlace(pagingPlaces, currentPage);
                    marked.push(currentPage);
                }
            }
        }
        if (isSuccess && !pageOccur && !marked.includes(page)) {
            pagingPlaces=
                addPlace(pagingPlaces, page, "", forCurrent);
            pageOccur = true;
            marked.push(page);
        }
        if (isSuccess) {
            for (let index = 1; index < settings.pagesAfter + 1;
                index++) {

                const currentPage = index + settings.pagesBefore
                    - settings.pagesAfter + page;
                const addLink = (currentPage < roundLimit)
                    && (currentPage > -1)
                        && !marked.includes(currentPage);
                if (addLink) {
                    pagingPlaces =
                        addPlace(pagingPlaces, currentPage);
                    marked.push(currentPage);
                }
            }
        }

        if (isSuccess && toFinish && !marked.includes(roundLimit - 1)
            && !marked.includes(roundLimit - 2)) {

            pagingPlaces =
                addPlace(pagingPlaces, Number.MAX_SAFE_INTEGER, "..");
            pagingPlaces = addPlace(pagingPlaces, roundLimit - 1);
            marked.push(roundLimit - 2);
            marked.push(roundLimit - 1);
        }

        if (isSuccess && !toFinish) {
            for (let index = 0; index < 2; index++) {
                const pageIndex = (roundLimit - 2 + index);
                const addLink = (pageIndex < roundLimit)
                    && !marked.includes(pageIndex);
                if (addLink && page !== index) {
                    pagingPlaces=addPlace(pagingPlaces, pageIndex);
                    marked.push(pageIndex);
                }
                if (addLink && page === pageIndex
                    && !marked.includes(pageIndex)) {
                    pagingPlaces=
                        addPlace(pagingPlaces, page, "", forCurrent);
                    marked.push(page);
                }
            }
        }
    }

    function addPlace(
        paging = [], index = Number.NEGATIVE_INFINITY,
        text = "", css = "") {
        paging.push({index:index,text:text,css:css});

        return paging;
    }

    const dispatch = createEventDispatcher();
    function browsePage(event) {
        const pageIndex = event.detail.index;
        dispatch('move', {
            index: pageIndex
        });
    }
</script>

<table class = "table table-hover-cells table-bordered">
    <tbody>
        <tr>
        {#each pagingPlaces as place (place.index)}
            <OrdinalPlace on:move="{browsePage}"
            {...place}>
            </OrdinalPlace>
        {/each}
        </tr>
    </tbody>
</table>
