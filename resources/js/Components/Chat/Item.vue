<script setup>
import moment from "moment";

const { currentUser, users } = defineProps({
	currentUser: Object,
	users: Array,
});

const emits = defineEmits([
	"fetchConversation",
	"fetchCurrentUser",
	"listenMessage",
]);

const loadMessages = async (id) => {
	emits("fetchCurrentUser", id);
	emits("fetchConversation", id);
	emits("listenMessage", id);
};
</script>

<template>
	<div
		class="divide-y-2 overflow-y-scroll pr-6 scrollbar-thin scrollbar-track-gray-400 scrollbar-thumb-black scrollbar-track-rounded-full scrollbar-thumb-rounded-full"
	>
		<button
			:class="`flex w-full gap-x-4 p-3 text-start hover:bg-gray-300/50${
				currentUser.id === user.user.id ? ' bg-gray-300/50' : ''
			}`"
			v-for="user in users"
			:key="user.user.id"
			@click="loadMessages(user.user.id)"
		>
			<img
				class="h-10 w-10 rounded-full"
				:src="`https://via.placeholder.com/200x200?text=${user.user.name
					.split(' ')
					.map((n) => n.charAt(0).toUpperCase())
					.join('')}`"
				alt="Demo Image"
			/>
			<div class="flex-1">
				<div class="flex justify-between">
					<div>
						<span class="block sm:text-lg">{{ user.user.name }}</span>
						<span class="block text-xs text-gray-400 sm:text-sm">{{
							user.last_message
						}}</span>
					</div>
					<div class="mt-1.5 text-xs">
						{{ moment(user.updated_at).format("HH:mm") }}
					</div>
				</div>
			</div>
		</button>
	</div>
</template>
