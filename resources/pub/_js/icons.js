import {
  createIcons,
  Star,
  Calendar,
  User,
  MessageCircle,
  Search,
  BookOpen,
  Package,
  Inbox,
  Flag,
  BadgeEuro,
} from "lucide";

export const iconRegistry = {
  Star,
  Calendar,
  User,
  MessageCircle,
  Search,
  BookOpen,
  Package,
  Inbox,
  Flag,
  BadgeEuro,
};

export function renderIcons() {
  createIcons({ icons: iconRegistry });
}

document.addEventListener("DOMContentLoaded", renderIcons());
