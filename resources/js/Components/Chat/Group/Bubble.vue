<script setup>
import { usePage } from "@inertiajs/vue3";
import moment from "moment";
import { computed, onUpdated, ref } from "vue";

const bubble = ref(null);

const user = computed(() => usePage().props.auth.user);

const { messages } = defineProps({
	messages: Array,
});

onUpdated(() => {
	bubble.value.scrollTop = bubble.value.lastElementChild.offsetTop;
});
</script>

<template>
	<div
		class="flex w-full flex-1 flex-col gap-y-4 overflow-y-scroll py-3 pr-6 scrollbar-thin scrollbar-track-gray-400 scrollbar-thumb-black scrollbar-track-rounded-full scrollbar-thumb-rounded-full"
		ref="bubble"
	>
		<div
			class="flex flex-col gap-y-1.5"
			v-for="message in messages"
			:key="message.id"
		>
			<span
				:class="`text-sm${user.id === message.user.id ? ' self-end' : ''}`"
				>{{ message.user.name }}</span
			>
			<div
				:class="`flex items-end gap-x-2.5${
					user.id === message.user.id ? ' flex-row-reverse self-end' : ''
				}`"
			>
				<div
					:class="`w-fit space-x-2 rounded-xl p-4 ${
						user.id === message.user.id ? 'bg-blue-400' : 'bg-gray-400'
					}`"
				>
					<span class="text-white">{{ message.message }}</span>
				</div>
				<span class="text-xs">{{
					moment(message.created_at).format("HH:mm")
				}}</span>
			</div>
		</div>
	</div>
</template>
