import { generateHTML } from '@tiptap/core'
import { tiptapExtensions } from '@/config/tiptapExtensions'

export function tiptapJsonToHtml(json: Record<string, any>) {
  return generateHTML(json, tiptapExtensions())
}