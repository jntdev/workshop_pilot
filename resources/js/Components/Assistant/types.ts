export type AssistantStatus = 'pending' | 'processing' | 'answered' | 'failed';

export type AssistantActionStatus =
    | 'proposed'
    | 'confirming'
    | 'executed'
    | 'cancelled'
    | 'undone'
    | 'failed'
    | 'expired';

/** Action proposée par l'assistant : elle ne change rien tant qu'elle n'est pas confirmée. */
export interface AssistantAction {
    id: number;
    type: string;
    type_label: string;
    summary: string;
    warnings: string[];
    status: AssistantActionStatus;
    status_label: string;
    error: string | null;
    expires_at: string;
    confirmed_at: string | null;
    confirmed_by: string | null;
    can_undo: boolean;
    link: string | null;
}

export interface AssistantMessage {
    id: number;
    conversation_id: string;
    question: string;
    status: AssistantStatus;
    answer: string | null;
    error: string | null;
    consultations: string[];
    actions: AssistantAction[];
    created_at: string;
    answered_at: string | null;
}
