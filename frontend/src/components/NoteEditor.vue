<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] overflow-hidden">
      <!-- Header -->
      <div class="p-4 border-b border-gray-100">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold text-[#3F4369]">Create Note</h2>
          <button 
            @click="$emit('close')"
            class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors"
          >
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Main Content -->
      <div class="p-4 flex-1 overflow-y-auto">
        <!-- Text Editor -->
        <div class="mb-6">
          <div 
            ref="textEditor"
            @input="updateContent"
            @paste="handlePaste"
            @drop="handleTextEditorDrop"
            @dragover.prevent
            @dragenter.prevent
            contenteditable="true"
            class="min-h-[150px] w-full p-4 border border-gray-200 rounded-xl text-lg text-[#3F4369] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F34767] focus:border-transparent resize-none"
            :style="{ whiteSpace: 'pre-wrap', direction: 'ltr', textAlign: 'left' }"
            data-placeholder="What would you like to share?"
          ></div>
        </div>

        <!-- Media Upload Section - Bottom Left -->
        <div class="mb-6 flex items-start space-x-4">
          <!-- Drag & Drop Zone -->
          <div class="flex-shrink-0">
            <div 
              @drop="handleDrop"
              @dragover.prevent
              @dragenter.prevent
              class="relative border-2 border-dashed border-gray-300 rounded-lg p-4 text-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer w-32 h-24"
              @click="triggerFileInput"
            >
              <input 
                ref="fileInput"
                type="file"
                multiple
                accept="image/*,video/*"
                @change="handleFileSelect"
                class="hidden"
              >
              
              <div class="flex flex-col items-center justify-center h-full">
                <svg class="w-8 h-8 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-xs text-gray-600">Drag & drop</p>
                <p class="text-xs text-gray-600">or</p>
                <p class="text-xs text-blue-600 cursor-pointer font-medium">select a file</p>
              </div>
            </div>
          </div>
          
          <!-- Spacer for layout -->
          <div class="flex-1"></div>
        </div>

        <!-- Toolbar -->
        <div class="flex items-center space-x-6 mb-6 relative">
          <!-- Emoji Picker -->
          <div class="relative emoji-picker-container">
            <button @click="toggleEmojiPicker" class="text-gray-600 hover:text-[#3F4369] transition-colors">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4M12,6A6,6 0 0,0 6,12A6,6 0 0,0 12,18A6,6 0 0,0 18,12A6,6 0 0,0 12,6M12,8A4,4 0 0,1 16,12A4,4 0 0,1 12,16A4,4 0 0,1 8,12A4,4 0 0,1 12,8M9,9.5C9.83,9.5 10.5,10.17 10.5,11C10.5,11.83 9.83,12.5 9,12.5C8.17,12.5 7.5,11.83 7.5,11C7.5,10.17 8.17,9.5 9,9.5M15,9.5C15.83,9.5 16.5,10.17 16.5,11C16.5,11.83 15.83,12.5 15,12.5C14.17,12.5 13.5,11.83 13.5,11C13.5,10.17 14.17,9.5 15,9.5M12,13.5C13.5,13.5 15.25,14.5 15.25,16H8.75C8.75,14.5 10.5,13.5 12,13.5Z"/>
              </svg>
            </button>

            <!-- Emoji Picker Modal -->
            <div v-if="showEmojiPicker" class="absolute bottom-10 left-0 bg-white rounded-xl shadow-2xl border border-gray-200 w-72 z-50">
              <!-- Top Navigation -->
              <div class="flex items-center justify-between p-2 border-b border-gray-100">
                <div class="flex items-center space-x-1">
                  <button 
                    @click="setEmojiCategory('frequently')"
                    :class="['p-2 rounded-lg transition-colors', emojiCategory === 'frequently' ? 'bg-blue-100 text-blue-600' : 'text-gray-600 hover:bg-gray-100']"
                  >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4M12,6A6,6 0 0,0 6,12A6,6 0 0,0 12,18A6,6 0 0,0 18,12A6,6 0 0,0 12,6M12,8A4,4 0 0,1 16,12A4,4 0 0,1 12,16A4,4 0 0,1 8,12A4,4 0 0,1 12,8M9,9.5C9.83,9.5 10.5,10.17 10.5,11C10.5,11.83 9.83,12.5 9,12.5C8.17,12.5 7.5,11.83 7.5,11C7.5,10.17 8.17,9.5 9,9.5M15,9.5C15.83,9.5 16.5,10.17 16.5,11C16.5,11.83 15.83,12.5 15,12.5C14.17,12.5 13.5,11.83 13.5,11C13.5,10.17 14.17,9.5 15,9.5M12,13.5C13.5,13.5 15.25,14.5 15.25,16H8.75C8.75,14.5 10.5,13.5 12,13.5Z"/>
                    </svg>
                  </button>
                  <button 
                    @click="setEmojiCategory('smileys')"
                    :class="['p-2 rounded-lg transition-colors', emojiCategory === 'smileys' ? 'bg-blue-100 text-blue-600' : 'text-gray-600 hover:bg-gray-100']"
                  >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4M12,6A6,6 0 0,0 6,12A6,6 0 0,0 12,18A6,6 0 0,0 18,12A6,6 0 0,0 12,6M12,8A4,4 0 0,1 16,12A4,4 0 0,1 12,16A4,4 0 0,1 8,12A4,4 0 0,1 12,8M9,9.5C9.83,9.5 10.5,10.17 10.5,11C10.5,11.83 9.83,12.5 9,12.5C8.17,12.5 7.5,11.83 7.5,11C7.5,10.17 8.17,9.5 9,9.5M15,9.5C15.83,9.5 16.5,10.17 16.5,11C16.5,11.83 15.83,12.5 15,12.5C14.17,12.5 13.5,11.83 13.5,11C13.5,10.17 14.17,9.5 15,9.5M12,13.5C13.5,13.5 15.25,14.5 15.25,16H8.75C8.75,14.5 10.5,13.5 12,13.5Z"/>
                    </svg>
                  </button>
                  <button 
                    @click="setEmojiCategory('animals')"
                    :class="['p-2 rounded-lg transition-colors', emojiCategory === 'animals' ? 'bg-blue-100 text-blue-600' : 'text-gray-600 hover:bg-gray-100']"
                  >
                    🐕
                  </button>
                  <button 
                    @click="setEmojiCategory('food')"
                    :class="['p-2 rounded-lg transition-colors', emojiCategory === 'food' ? 'bg-blue-100 text-blue-600' : 'text-gray-600 hover:bg-gray-100']"
                  >
                    🍎
                  </button>
                  <button 
                    @click="setEmojiCategory('activities')"
                    :class="['p-2 rounded-lg transition-colors', emojiCategory === 'activities' ? 'bg-blue-100 text-blue-600' : 'text-gray-600 hover:bg-gray-100']"
                  >
                    🏀
                  </button>
                  <button 
                    @click="setEmojiCategory('travel')"
                    :class="['p-2 rounded-lg transition-colors', emojiCategory === 'travel' ? 'bg-blue-100 text-blue-600' : 'text-gray-600 hover:bg-gray-100']"
                  >
                    🚗
                  </button>
                  <button 
                    @click="setEmojiCategory('objects')"
                    :class="['p-2 rounded-lg transition-colors', emojiCategory === 'objects' ? 'bg-blue-100 text-blue-600' : 'text-gray-600 hover:bg-gray-100']"
                  >
                    💡
                  </button>
                </div>
                <button @click="closeEmojiPicker" class="p-1 text-gray-400 hover:text-gray-600">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>

              <!-- Search Bar -->
              <div class="p-2 border-b border-gray-100">
                <div class="relative">
                  <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                  <input 
                    v-model="emojiSearch"
                    type="text" 
                    placeholder="Search"
                    class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                </div>
              </div>

              <!-- Emoji Grid -->
              <div class="p-2 max-h-40 overflow-y-auto">
                <!-- Frequently Used -->
                <div v-if="emojiCategory === 'frequently'" class="space-y-4">
                  <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Frequently used</h3>
                    <div class="grid grid-cols-8 gap-1">
                      <button 
                        v-for="emoji in frequentlyUsedEmojis" 
                        :key="emoji"
                        @click="insertEmojiAtCursor(emoji)"
                        class="w-8 h-8 text-xl hover:bg-gray-100 rounded-lg flex items-center justify-center"
                      >
                        {{ emoji }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Smileys & People -->
                <div v-if="emojiCategory === 'smileys'" class="space-y-4">
                  <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Smileys & People</h3>
                    <div class="grid grid-cols-8 gap-1">
                      <button 
                        v-for="emoji in smileyEmojis" 
                        :key="emoji"
                        @click="insertEmojiAtCursor(emoji)"
                        class="w-8 h-8 text-xl hover:bg-gray-100 rounded-lg flex items-center justify-center"
                      >
                        {{ emoji }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Animals -->
                <div v-if="emojiCategory === 'animals'" class="space-y-4">
                  <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Animals & Nature</h3>
                    <div class="grid grid-cols-8 gap-1">
                      <button 
                        v-for="emoji in animalEmojis" 
                        :key="emoji"
                        @click="insertEmojiAtCursor(emoji)"
                        class="w-8 h-8 text-xl hover:bg-gray-100 rounded-lg flex items-center justify-center"
                      >
                        {{ emoji }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Food -->
                <div v-if="emojiCategory === 'food'" class="space-y-4">
                  <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Food & Drink</h3>
                    <div class="grid grid-cols-8 gap-1">
                      <button 
                        v-for="emoji in foodEmojis" 
                        :key="emoji"
                        @click="insertEmojiAtCursor(emoji)"
                        class="w-8 h-8 text-xl hover:bg-gray-100 rounded-lg flex items-center justify-center"
                      >
                        {{ emoji }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Activities -->
                <div v-if="emojiCategory === 'activities'" class="space-y-4">
                  <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Activities</h3>
                    <div class="grid grid-cols-8 gap-1">
                      <button 
                        v-for="emoji in activityEmojis" 
                        :key="emoji"
                        @click="insertEmojiAtCursor(emoji)"
                        class="w-8 h-8 text-xl hover:bg-gray-100 rounded-lg flex items-center justify-center"
                      >
                        {{ emoji }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Travel -->
                <div v-if="emojiCategory === 'travel'" class="space-y-4">
                  <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Travel & Places</h3>
                    <div class="grid grid-cols-8 gap-1">
                      <button 
                        v-for="emoji in travelEmojis" 
                        :key="emoji"
                        @click="insertEmojiAtCursor(emoji)"
                        class="w-8 h-8 text-xl hover:bg-gray-100 rounded-lg flex items-center justify-center"
                      >
                        {{ emoji }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Objects -->
                <div v-if="emojiCategory === 'objects'" class="space-y-4">
                  <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Objects</h3>
                    <div class="grid grid-cols-8 gap-1">
                      <button 
                        v-for="emoji in objectEmojis" 
                        :key="emoji"
                        @click="insertEmojiAtCursor(emoji)"
                        class="w-8 h-8 text-xl hover:bg-gray-100 rounded-lg flex items-center justify-center"
                      >
                        {{ emoji }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Bottom Input -->
              <div class="p-2 border-t border-gray-100">
                <div class="flex items-center space-x-3">
                  <span class="text-xl">👆</span>
                  <input 
                    v-model="emojiInput"
                    type="text" 
                    placeholder="Pick an emoji..."
                    class="flex-1 px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                  <div class="w-6 h-6 bg-orange-200 rounded-full"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Title Input -->
        <div class="mb-6">
          <input 
            v-model="noteTitle"
            type="text"
            placeholder="Note title (optional)"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-[#3F4369] focus:outline-none focus:ring-2 focus:ring-[#F34767] focus:border-transparent"
          >
        </div>
      </div>

      <!-- Footer -->
      <div class="p-4 border-t border-gray-100 bg-gray-50">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <!-- Additional tools can be added here -->
          </div>
          
          <button 
            @click="saveNote"
            :disabled="!hasContent"
            class="px-8 py-3 bg-[#F34767] text-white rounded-xl font-medium hover:bg-[#e63d5a] disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center space-x-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <span>Save Note</span>
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'

const props = defineProps({
  initialContent: {
    type: String,
    default: ''
  },
  initialTitle: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['close', 'save'])

const textEditor = ref(null)
const fileInput = ref(null)
const noteTitle = ref(props.initialTitle)

const hasContent = computed(() => {
  contentUpdated.value
  if (!textEditor.value) return false
  const text = textEditor.value.innerText?.trim()
  const hasImages = textEditor.value.querySelectorAll('img').length > 0
  const hasTitle = noteTitle.value && noteTitle.value.trim().length > 0
  return (text && text.length > 0) || hasImages || hasTitle
})

const contentUpdated = ref(0)
const forceUpdate = () => {
  contentUpdated.value++
}

onMounted(() => {
  nextTick(() => {
    if (textEditor.value && props.initialContent) {
      textEditor.value.innerHTML = props.initialContent
    }
  })
})

const updateContent = () => {
  forceUpdate()
}

const handlePaste = (e) => {
  e.preventDefault()
  const text = e.clipboardData.getData('text/plain')
  document.execCommand('insertText', false, text)
  forceUpdate()
}

const handleTextEditorDrop = (e) => {
  e.preventDefault()
  const files = Array.from(e.dataTransfer.files)
  processFiles(files)
  forceUpdate()
}

const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleFileSelect = (e) => {
  const files = Array.from(e.target.files)
  processFiles(files)
  forceUpdate()
}

const handleDrop = (e) => {
  e.preventDefault()
  const files = Array.from(e.dataTransfer.files)
  processFiles(files)
  forceUpdate()
}

const processFiles = (files) => {
  files.forEach(file => {
    if (file.type.startsWith('image/') || file.type.startsWith('video/')) {
      const reader = new FileReader()
      reader.onload = (e) => {
        if (file.type.startsWith('image/')) {
          insertImageIntoText(e.target.result, file.name)
        }
      }
      reader.readAsDataURL(file)
    }
  })
}


const insertImageIntoText = (imageUrl, imageName) => {
  if (textEditor.value) {
    textEditor.value.focus()
    
    const selection = window.getSelection()
    if (selection.rangeCount > 0) {
      const range = selection.getRangeAt(0)
      
      const img = document.createElement('img')
      img.src = imageUrl
      img.alt = imageName
      img.style.maxWidth = '120px'
      img.style.maxHeight = '80px'
      img.style.objectFit = 'cover'
      img.style.borderRadius = '6px'
      img.style.margin = '2px'
      img.style.display = 'inline-block'
      img.style.verticalAlign = 'middle'
      
      range.insertNode(img)
      
      const spaceNode = document.createTextNode(' ')
      range.insertNode(spaceNode)
      
      range.setStartAfter(spaceNode)
      range.setEndAfter(spaceNode)
      selection.removeAllRanges()
      selection.addRange(range)
    } else {
      const img = document.createElement('img')
      img.src = imageUrl
      img.alt = imageName
      img.style.maxWidth = '120px'
      img.style.maxHeight = '80px'
      img.style.objectFit = 'cover'
      img.style.borderRadius = '6px'
      img.style.margin = '2px'
      img.style.display = 'inline-block'
      img.style.verticalAlign = 'middle'
      
      textEditor.value.appendChild(img)
      
      const spaceNode = document.createTextNode(' ')
      textEditor.value.appendChild(spaceNode)
    }
    
    const event = new Event('input', { bubbles: true })
    textEditor.value.dispatchEvent(event)
    
    forceUpdate()
  }
}

const toggleEmojiPicker = () => {
  showEmojiPicker.value = !showEmojiPicker.value
}

const closeEmojiPicker = () => {
  showEmojiPicker.value = false
}

const setEmojiCategory = (category) => {
  emojiCategory.value = category
}

const insertEmojiAtCursor = (emoji) => {
  if (textEditor.value) {
    textEditor.value.focus()
    
    const selection = window.getSelection()
    if (selection.rangeCount > 0) {
      const range = selection.getRangeAt(0)
      
      const textNode = document.createTextNode(emoji)
      range.insertNode(textNode)
      
      range.setStartAfter(textNode)
      range.setEndAfter(textNode)
      selection.removeAllRanges()
      selection.addRange(range)
    } else {
      textEditor.value.innerHTML += emoji
    }
    
    const event = new Event('input', { bubbles: true })
    textEditor.value.dispatchEvent(event)
    
    if (!frequentlyUsedEmojis.value.includes(emoji)) {
      frequentlyUsedEmojis.value.unshift(emoji)
      if (frequentlyUsedEmojis.value.length > 9) {
        frequentlyUsedEmojis.value.pop()
      }
    }
    
    forceUpdate()
  }
  
  closeEmojiPicker()
}


const showEmojiPicker = ref(false)
const emojiCategory = ref('frequently')
const emojiSearch = ref('')
const emojiInput = ref('')

const frequentlyUsedEmojis = ref(['👍', '😀', '😘', '😍', '😆', '😜', '🙂', '😭', '🤯'])

const smileyEmojis = ref([
  '😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '🙃', '😉', '😊', '😇', '🥰', '😍', '🤩',
  '😘', '😗', '😚', '😙', '😋', '😛', '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🤫', '🤔', '🤐', '🤨',
  '😐', '😑', '😶', '😏', '😒', '🙄', '😬', '🤥', '😌', '😔', '😪', '🤤', '😴', '😷', '🤒', '🤕',
  '🤢', '🤮', '🤧', '🥵', '🥶', '🥴', '😵', '🤯', '🤠', '🥳', '😎', '🤓', '🧐', '😕', '😟', '🙁',
  '☹️', '😮', '😯', '😲', '😳', '🥺', '😦', '😧', '😨', '😰', '😥', '😢', '😭', '😱', '😖', '😣',
  '😞', '😓', '😩', '😫', '🥱', '😤', '😡', '😠', '🤬', '😈', '👿', '💀', '☠️', '💩', '🤡', '👹'
])

const animalEmojis = ref([
  '🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐽', '🐸', '🐵',
  '🙈', '🙉', '🙊', '🐒', '🐔', '🐧', '🐦', '🐤', '🐣', '🐥', '🦆', '🦅', '🦉', '🦇', '🐺', '🐗',
  '🐴', '🦄', '🐝', '🐛', '🦋', '🐌', '🐞', '🐜', '🦟', '🦗', '🕷️', '🕸️', '🦂', '🐢', '🐍', '🦎',
  '🦖', '🦕', '🐙', '🦑', '🦐', '🦞', '🦀', '🐡', '🐠', '🐟', '🐬', '🐳', '🐋', '🦈', '🐊', '🐅'
])

const foodEmojis = ref([
  '🍎', '🍐', '🍊', '🍋', '🍌', '🍉', '🍇', '🍓', '🫐', '🍈', '🍒', '🍑', '🥭', '🍍', '🥥', '🥝',
  '🍅', '🍆', '🥑', '🥦', '🥬', '🥒', '🌶️', '🫒', '🌽', '🥕', '🫑', '🥔', '🍠', '🥐', '🥖', '🍞',
  '🥨', '🥯', '🧀', '🥚', '🍳', '🧈', '🥞', '🧇', '🥓', '🥩', '🍗', '🍖', '🦴', '🌭', '🍔', '🍟',
  '🍕', '🥪', '🥙', '🧆', '🌮', '🌯', '🫔', '🥗', '🥘', '🫕', '🥫', '🍝', '🍜', '🍲', '🍛', '🍣'
])

const activityEmojis = ref([
  '⚽', '🏀', '🏈', '⚾', '🥎', '🎾', '🏐', '🏉', '🎱', '🪀', '🏓', '🏸', '🏒', '🏑', '🥍', '🏏',
  '🪃', '🥅', '⛳', '🪁', '🏹', '🎣', '🤿', '🥊', '🥋', '🎽', '🛹', '🛷', '⛸️', '🥌', '🎿', '⛷️',
  '🏂', '🪂', '🏋️‍♀️', '🏋️‍♂️', '🤼‍♀️', '🤼‍♂️', '🤸‍♀️', '🤸‍♂️', '⛹️‍♀️', '⛹️‍♂️', '🤺', '🤾‍♀️', '🤾‍♂️', '🏌️‍♀️', '🏌️‍♂️', '🏇',
  '🧘‍♀️', '🧘‍♂️', '🏄‍♀️', '🏄‍♂️', '🏊‍♀️', '🏊‍♂️', '🤽‍♀️', '🤽‍♂️', '🚣‍♀️', '🚣‍♂️', '🧗‍♀️', '🧗‍♂️', '🚵‍♀️', '🚵‍♂️', '🚴‍♀️', '🚴‍♂️'
])

const travelEmojis = ref([
  '🚗', '🚕', '🚙', '🚌', '🚎', '🏎️', '🚓', '🚑', '🚒', '🚐', '🛻', '🚚', '🚛', '🚜', '🏍️', '🛵',
  '🚲', '🛴', '🛹', '🛼', '🚁', '✈️', '🛩️', '🛫', '🛬', '🪂', '💺', '🚀', '🛸', '🚉', '🚊', '🚝',
  '🚞', '🚋', '🚃', '🚋', '🚋', '🚋', '🚋', '🚋', '🚋', '🚋', '🚋', '🚋', '🚋', '🚋', '🚋', '🚋'
])

const objectEmojis = ref([
  '💡', '🔦', '🕯️', '🪔', '🔋', '🔌', '💻', '🖥️', '🖨️', '⌨️', '🖱️', '🖲️', '💽', '💾', '💿', '📀',
  '📼', '📷', '📸', '📹', '🎥', '📽️', '🎞️', '📞', '☎️', '📟', '📠', '📺', '📻', '🎙️', '🎚️', '🎛️',
  '🧭', '⏱️', '⏲️', '⏰', '🕰️', '⌛', '⏳', '📡', '🔋', '🔌', '💎', '🔍', '🔎', '🕯️', '💡', '🔦'
])


const saveNote = () => {
  if (!hasContent.value) {
    alert('Please enter some content for your note')
    return
  }

  const noteData = {
    title: noteTitle.value.trim() || 'Untitled Note',
    content: textEditor.value?.innerHTML || '',
    created_at: new Date().toISOString()
  }

  emit('save', noteData)
}

const addPlaceholder = () => {
  if (textEditor.value && !textEditor.value.innerText.trim()) {
    textEditor.value.setAttribute('data-placeholder', 'What would you like to share?')
  }
}

const handleClickOutside = (event) => {
  if (showEmojiPicker.value && !event.target.closest('.emoji-picker-container')) {
    closeEmojiPicker()
  }
}

onMounted(() => {
  addPlaceholder()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
[contenteditable]:empty:before {
  content: attr(data-placeholder);
  color: #9CA3AF;
  pointer-events: none;
}

[contenteditable]:focus:before {
  content: none;
}
</style>
