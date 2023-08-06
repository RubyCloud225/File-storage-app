import lzma
data = 'fileInput'
f = lzma.open(filename)
f.write(data)
f.close()

class compression (fileInput, filename):
    def write_file():
        obj = lzma.LZMAFile(filename, mode="wb")
        obj.write(data)
        obj.close()
        return write_file
    def read_file():
        obj = lzma.LZMAFile(filename, mode='rb')
        data=obj.read()
        return data