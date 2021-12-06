
class TestOut {
	private int n; // 선언한 클래스 밖에서는 접근할 수 없다.(write/read 불가)
	private String str;
	private float realnumber;
	
	public TestOut() {
	}

	public TestOut(String str, float realnumber) {
		this.str = str;
		this.realnumber = realnumber;
		this.n=(int)this.realnumber;
	}

	public TestOut(int n, String str) {
		this.n = n;
		this.str = str;
	}

	public TestOut(int n) {
		this.n = n;
	}

	public int getN() {
		return n;
	}

	public String getStr() {
		return str;
	}

	public float getRealnumber() {
		return realnumber;
	}

	public void setN(int n) {
		this.n = n;
	}

	public void setStr(String str) {
		this.str = str;
	}

	public void setRealnumber(float realnumber) {
		this.realnumber = realnumber;
	}
	
}
