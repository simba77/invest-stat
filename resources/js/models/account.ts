export interface Account {
  id: number,
  name: string,
  balance: number,
  blockGroups: BlockGroups[],
}

export interface BlockGroups {
  name: string,
  blocked: boolean,
  items: CurrencyGroup[]
}

export interface CurrencyGroup {
  name: string,
  items: AssetsGroup[]
}

export interface AssetsGroup {
  group: boolean,
  id?: number,
  accountId: number,
  ticker: string,
  name: string,
  stockMarket: string,
  blocked: boolean | null,
  quantity: number,
  avgBuyPrice: number,
  fullBuyPrice: number,
  avgTargetPrice: number,
  fullTargetPrice: number,
  currentPrice: number,
  fullCurrentPrice: number,
  profit: number,
  profitPercent: number,
  fullCommission: number,
  targetProfit: number,
  fullTargetProfit: number,
  fullTargetProfitPercent: number,
  groupPercent: number,
  currency: string,
  showItems: boolean,
  items: []
}

export interface Asset {
  id: number,
  createdAt: string,
  updatedAt: string,
  accountId: number,
  ticker: string,
  name: string,
  stockMarket: string,
  blocked: boolean | null,
  quantity: number,
  avgBuyPrice: number,
  fullBuyPrice: number,
  avgTargetPrice: number,
  fullTargetPrice: number,
  currentPrice: number,
  fullCurrentPrice: number,
  profit: number,
  profitPercent: number,
  commission: number,
  targetProfit: number,
  fullTargetProfit: number,
  fullTargetProfitPercent: number,
  groupPercent: number,
  currency: string,
}

