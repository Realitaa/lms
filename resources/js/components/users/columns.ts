import type { ColumnDef } from '@tanstack/vue-table';
import { ArrowUpDown, Pencil, Trash2 } from 'lucide-vue-next';
import { h } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { User } from '@/types';
import currentUserId from '@/utils/currentUserId';

export const columns: ColumnDef<User>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) =>
            h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () =>
                        column.toggleSorting(
                            column.getIsSorted() === 'asc',
                        ),
                },
                () => ['Name', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
            ),
        cell: ({ row }) => row.getValue('name'),
    },
    {
        accessorKey: 'email',
        header: ({ column }) =>
            h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () =>
                        column.toggleSorting(
                            column.getIsSorted() === 'asc',
                        ),
                },
                () => ['Email', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
            ),
        cell: ({ row }) => row.getValue('email'),
    },
    {
        accessorKey: 'role',
        header: ({ column }) =>
            h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () =>
                        column.toggleSorting(
                            column.getIsSorted() === 'asc',
                        ),
                },
                () => ['Role', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
            ),
        cell: ({ row }) => {
            const role = row.getValue('role') as string;
            const variant =
                role === 'admin'
                    ? 'default'
                    : role === 'editor'
                      ? 'secondary'
                      : 'outline';
            return h(
                Badge,
                { variant },
                () => role.charAt(0).toUpperCase() + role.slice(1),
            );
        },
    },
    {
        id: 'actions',
        header: () => h('span', 'Actions'),
        cell: ({ row, table }) => {
            const user = row.original;
            const meta = table.options.meta as {
                onEdit: (user: User) => void;
                onDelete: (user: User) => void;
            };

            return h('div', { class: 'flex items-center gap-1' }, [
                h(
                    Button,
                    {
                        variant: 'ghost',
                        size: 'icon',
                        class: 'h-8 w-8',
                        onClick: () => meta.onEdit(user),
                    },
                    () =>
                        h(Pencil, {
                            class: 'h-4 w-4 text-muted-foreground',
                        }),
                ),
                ...(currentUserId !== user.id
                    ? [
                          h(
                              Button,
                              {
                                  variant: 'ghost',
                                  size: 'icon',
                                  class: 'h-8 w-8',
                                  onClick: () => meta.onDelete(user),
                              },
                              () =>
                                  h(Trash2, {
                                      class: 'h-4 w-4 text-destructive',
                                  }),
                          ),
                      ]
                    : []),
            ]);
        },
    },
];
