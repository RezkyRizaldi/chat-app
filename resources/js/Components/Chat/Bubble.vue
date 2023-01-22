<script setup>
import { usePage } from "@inertiajs/vue3";
import moment from "moment";
import { computed, onUpdated, ref } from "vue";

const { conversation } = defineProps({
	conversation: Object,
});

const bubble = ref(null);

const user = computed(() => usePage().props.auth.user);

onUpdated(() => {
	bubble.value.scrollTop = bubble.value.lastElementChild.offsetTop;
});
</script>

<template>
	<div
		class="flex h-64 flex-col gap-y-4 overflow-y-scroll py-3 pr-6 scrollbar-thin scrollbar-track-gray-400 scrollbar-thumb-black scrollbar-track-rounded-full scrollbar-thumb-rounded-full"
		ref="bubble"
	>
		<div
			:class="`${user.id === message.user_id ? 'self-end' : ''}`"
			v-for="message in conversation.messages"
			:key="message.id"
		>
			<div
				:class="`w-fit space-x-2 rounded-xl p-4 ${
					user.id === message.user_id ? 'bg-blue-400' : 'bg-gray-400'
				}`"
			>
				<span class="text-white">{{ message.message }}</span>
				<span class="text-[8px] text-white">{{
					moment(message.created_at).format("LT")
				}}</span>
			</div>
		</div>
	</div>
</template>
