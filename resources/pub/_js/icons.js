import {
  createIcons,
  Star,
  Calendar,
  User,
  MessageCircle,
  MessageCircleOff,
  Search,
  BookOpen,
  Inbox,
  Flag,
  LogOut,
  Package,
  PackageX,
  PackageOpen,
} from "lucide";

export const iconRegistry = {
  Star,
  Calendar,
  User,
  MessageCircle,
  MessageCircleOff,
  Search,
  BookOpen,
  Inbox,
  Flag,
  LogOut,
  Package,
  PackageX,
  PackageOpen,
};

export function renderIcons() {
  createIcons({ icons: iconRegistry });
}

document.addEventListener("DOMContentLoaded", renderIcons());
