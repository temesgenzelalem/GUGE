export default function Loading() {
  return (
    <div className="px-10 py-14 animate-pulse">
      {/* Page header */}
      <div className="mb-10">
        <div className="h-3 w-28 bg-paper-3 rounded mb-3" />
        <div className="h-9 w-72 bg-paper-3 rounded mb-3" />
        <div className="h-4 w-96 bg-paper-3 rounded" />
      </div>
      {/* Card skeletons */}
      <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        {Array.from({ length: 8 }).map((_, i) => (
          <div key={i} className="rounded-[18px] overflow-hidden border border-black/8">
            <div className="h-44 shimmer" />
            <div className="p-4 space-y-2">
              <div className="h-2.5 w-20 bg-paper-3 rounded" />
              <div className="h-4 w-36 bg-paper-3 rounded" />
              <div className="h-3 w-full bg-paper-3 rounded" />
              <div className="h-3 w-4/5 bg-paper-3 rounded" />
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
