<script>
    export let index = -1;
    export let text = "";
    export let css = "";

    let number = index + 1;

    function skip() {
        return !(text === "");
    }

    let letSkip = skip();

    import {createEventDispatcher} from "svelte";

    const dispatch = createEventDispatcher();

    function moveTo() {
        if (!letSkip) {
            dispatch("move", {index: index});
        }
    }
</script>
<td class="{css}" on:click="{moveTo}">
    {#if letSkip}
        {text}
    {/if}
    {#if !letSkip}
        {number}
    {/if}
</td>
