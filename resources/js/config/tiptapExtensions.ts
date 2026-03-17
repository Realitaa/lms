import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'
import Strike from '@tiptap/extension-strike'
import Image from '@tiptap/extension-image'
import Youtube from '@tiptap/extension-youtube'
import TextAlign from '@tiptap/extension-text-align'
import { Mathematics } from '@tiptap/extension-mathematics'
import 'katex/dist/katex.min.css'

export type TiptapExtensionOptions = {
    mathematics?: any
}

export const tiptapExtensions = (options: TiptapExtensionOptions = {}) => [
    StarterKit,
    Underline,
    Strike,
    Image,
    Youtube.configure({
        controls: false,
        nocookie: true,
    }),
    TextAlign.configure({
        types: ['heading', 'paragraph'],
    }),
    options.mathematics ? Mathematics.configure(options.mathematics) : Mathematics,
]
