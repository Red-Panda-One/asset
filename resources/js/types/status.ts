export type BaseStatus = 'Available' | 'In Use' | 'Maintenance' | 'Retired' | 'Lost' | 'Faulty';
export type Status = BaseStatus | `Assigned to ${string}`;

export interface StatusOption {
    value: Status;
    label: string;
    type: 'base' | 'assigned';
}

export const baseStatusOptions: StatusOption[] = [
    { value: 'Available', label: 'Available', type: 'base' },
    { value: 'In Use', label: 'In Use', type: 'base' },
    { value: 'Maintenance', label: 'Maintenance', type: 'base' },
    { value: 'Retired', label: 'Retired', type: 'base' },
    { value: 'Lost', label: 'Lost', type: 'base' },
    { value: 'Faulty', label: 'Faulty', type: 'base' },
];
