export function pluralize(n, one, few, many) {
  if (n === 1)
    return one;

  if (n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 12 || n % 100 > 14))
    return few;

  return many;
}
