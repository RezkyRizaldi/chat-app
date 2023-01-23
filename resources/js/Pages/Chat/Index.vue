<script setup>
import { ChatBubbleLeftRightIcon } from "@heroicons/vue/24/outline";
import { PaperAirplaneIcon } from "@heroicons/vue/24/solid";
import { Head, useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import ChatBubble from "@/Components/Chat/Bubble.vue";
import ChatItem from "@/Components/Chat/Item.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const conversation = ref({});
const users = ref([]);
const currentUser = ref({});
const loading = ref(true);

const form = useForm({ message: "" });

const fetchUsers = async () => {
	const { data } = await axios.get(route("users.index"));

	users.value = data;
};

const fetchCurrentUser = async (id) => {
	loading.value = true;

	const { data } = await axios.get(route("users.show", { user: id }));

	currentUser.value = data;
};

const fetchConversation = async (id) => {
	const { data } = await axios.get(
		route("conversations.show", { receiverId: id })
	);

	conversation.value = data;
	loading.value = false;
};

const listenMessage = async (id) => {
	if (conversation.value.id !== undefined) {
		Echo.leave("chat." + conversation.value.id);
	}

	const { data } = await axios.get(
		route("conversations.show", { receiverId: id })
	);

	Echo.private(`chat.${data.id}`).listen("ChatEvent", ({ message }) => {
		conversation.value.messages.push({ message: message.message });
	});
};

const sendMessage = () => {
	form.post(
		route("conversations.store", { conversation: conversation.value.id }),
		{
			onSuccess: () => {
				form.reset();
				fetchConversation(currentUser.value.id);
				fetchUsers();
			},
			preserveScroll: true,
		}
	);
};

onMounted(() => {
	fetchUsers();
});
</script>

<template>
	<Head title="Chats" />

	<AuthenticatedLayout>
		<div class="py-12">
			<div class="mx-auto max-w-xs sm:max-w-7xl sm:px-6 lg:px-8">
				<div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
					<div class="h-[31rem] p-6 text-gray-900 sm:h-[28rem]">
						<div
							class="grid h-full grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-y-0 sm:divide-x-2"
						>
							<ChatItem
								:users="users"
								:currentUser="currentUser"
								@fetchCurrentUser="fetchCurrentUser"
								@fetchConversation="fetchConversation"
								@listenMessage="listenMessage"
							/>
							<div
								class="flex flex-col items-center justify-center gap-y-2.5 py-3"
								v-if="
									!Object.keys(currentUser).length &&
									!Object.keys(conversation).length
								"
							>
								<ChatBubbleLeftRightIcon
									class="h-10 w-10 text-gray-400 sm:h-20 sm:w-20"
								/>
								<span class="text-center text-sm italic text-gray-400"
									>Mulai mengobrol dengan orang-orang.</span
								>
							</div>
							<div class="flex items-center justify-center" v-else-if="loading">
								<div role="status">
									<svg
										aria-hidden="true"
										class="h-20 w-20 animate-spin fill-blue-600 text-gray-200 dark:text-gray-600"
										viewBox="0 0 100 101"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
									>
										<path
											d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
											fill="currentColor"
										/>
										<path
											d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
											fill="currentFill"
										/>
									</svg>
									<span class="sr-only">Loading...</span>
								</div>
							</div>
							<div class="flex flex-col justify-between pl-6" v-else>
								<div class="flex items-center gap-x-2.5">
									<img
										class="h-10 w-10 rounded-full"
										:src="`https://via.placeholder.com/200x200${
											currentUser.name &&
											`?text=${currentUser.name
												.split(' ')
												.map((n) => n.charAt(0).toUpperCase())
												.join('')}`
										}`"
										alt="Demo Image"
									/>
									<div class="flex-1">
										<span class="block text-lg">{{ currentUser.name }}</span>
									</div>
								</div>
								<div
									class="flex flex-col items-center justify-center gap-y-2.5 py-3"
									v-if="conversation.messages && !conversation.messages.length"
								>
									<ChatBubbleLeftRightIcon
										class="h-10 w-10 text-gray-400 sm:h-20 sm:w-20"
									/>
									<span class="text-center text-sm italic text-gray-400"
										>Obrolan belum ada, silahkan memulai obrolan.</span
									>
								</div>
								<ChatBubble :conversation="conversation" v-else />
								<div class="w-full">
									<form
										class="flex items-center gap-x-4"
										@submit.prevent="sendMessage"
									>
										<div class="flex-1">
											<TextInput
												aria-label="Chat"
												type="text"
												class="block w-full"
												placeholder="Kirim pesan"
												v-model="form.message"
												required
												autofocus
											/>
										</div>

										<div>
											<PrimaryButton
												:class="{ 'opacity-25': form.processing }"
												:disabled="form.processing"
											>
												<PaperAirplaneIcon class="h-6 w-6" />
											</PrimaryButton>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
