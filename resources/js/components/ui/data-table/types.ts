export interface TableProps {
  class?: string;
}

export interface TableColumnProps<T = any> {
  field: keyof T | ((item: T) => any);
  header: string;
  align?: 'left' | 'center' | 'right';
  class?: string;
  renderCell?: (item: T) => any;
}